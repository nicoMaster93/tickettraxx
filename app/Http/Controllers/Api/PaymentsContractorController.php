<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConfigModel;
use App\Models\ContractorsModel;
use App\Models\DeductionsModel;
use App\Models\DeductionVehiclesModel;
use App\Models\OtherPaymentsModel;
use App\Models\SettlementsModel;
use App\Models\TicketsModel;
use App\Models\VehiclesModel;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentsContractorController extends Controller
{

    /**
     * Collection of payments
     * Shows all payments by contractor
     * 
	 * @group  v 1.0.0
     * 
     * @bodyParam start_date String optional Start date for payments filter (Format: YYYY-MM-DD).
     * @bodyParam end_date String optional End date for payments filter (Format: YYYY-MM-DD).
     * 
     * @authenticated
     * 
	 */
    public function index(Request $request){
        
        $contractor = auth()->user()->contractor;

        $start = date("Y-m-01");
        $final = date("Y-m-t");
        if($request->has("start_date") && !empty($request->input("start_date"))) $start = $request->input("start_date");
        if($request->has("end_date") && !empty($request->input("end_date"))) $final = $request->input("end_date");
        
        $settlements = SettlementsModel::
        select(DB::raw('CONCAT("/api/payments/pdf/",fk_payment) AS urlPDF'),"fk_contractor",
        "fk_payment", "fk_settlement_state", DB::raw('sum(for_contractor) as total'))
        ->with(['payment:id,date_pay','contractor:id,company_name', 'settlement_state:id,name'])
        ->whereHas('payment', function($query) use($start, $final) {
            $query->where("payments.date_pay",">=",$start)
            ->where("payments.date_pay", "<=",$final);
        })
        ->where("fk_contractor","=",$contractor->id)
        ->groupBy( "fk_contractor", "fk_payment", "fk_settlement_state");
        
        //Page and number of records by consult
        $page = 0;
        $records = 6;
        if($request->has("page")){
            $page = $request->input("page");
        }        
        $settlements = $settlements->skip($page*$records)->take($records)->get();
        
        foreach($settlements as $key => $settlement){
            $settlement->total = "$ ".number_format($settlement->total, 2);
            $settlements[$key] = $settlement;
        }
        
        return response()->json([
            "success" => true,
            "message" => "Payments showed successfully!",
            "data" => [
                "settlements" => $settlements,
                "start_date" => $start,
                "end_date" => $final
            ]
        ], 200);

       

    }

    public function pdf($id_payment){

        $settlements = SettlementsModel::where("fk_payment","=",$id_payment)
        ->get();

        $id_contractor = $settlements[0]->fk_contractor;
        $contractor = ContractorsModel::find($id_contractor);
        $vehicles = VehiclesModel::where("fk_contractor","=",$id_contractor)->get();
        $data = array();
        
        foreach($settlements as $settlement){
            $data[$settlement->id] = [
                "settlement" => $settlement
            ];

            $other_payments = OtherPaymentsModel::join("settlements_other_payments as sop","sop.fk_other_payments","=","other_payments.id")
            ->where("sop.fk_settlement","=",$settlement->id)
            ->get();

            $data[$settlement->id]["other_payments"] = $other_payments;
            
            
            
            

            $deductions_sum_fee = DeductionsModel::select(DB::raw("sum(deductions.total_value) as total_value"))
            ->join("settlements_deduction as sd","sd.fk_deduction","=","deductions.id")
            ->where("sd.fk_settlement","=",$settlement->id)
            ->where("deductions.fk_deduction_type","=","2")
            ->groupBy("deductions.id")
            ->first();

            $deductionTotal = ($deductions_sum_fee->total_value ?? 0);

            $deductions_sum_fee = DeductionsModel::select(DB::raw("sum(dv.total) as suma_total, count(dv.id) as cuenta"))
            ->join("deduction_vehicles as dv","dv.fk_deduction","deductions.id")
            ->join("settlements_deduction as sd","sd.fk_deduction","=","deductions.id")
            ->where("sd.fk_settlement","=",$settlement->id)
            ->where("deductions.fk_deduction_type","=","2")
            ->groupBy("deductions.id")
            ->first();

            
            $dvTotal = ($deductions_sum_fee->suma_total ?? 0);
            $cuenta = ($deductions_sum_fee->cuenta ?? 0);
            $feeUnitario = 0;
            if($cuenta > 0){
                $feeUnitario = ($deductionTotal - $dvTotal) / $cuenta;
            }

            $data[$settlement->id]["feeUnitario"] = $feeUnitario;

            foreach($vehicles as $vehicle){

                $tickets = TicketsModel::select("tickets.*")
                ->join("settlements_tickets as st","st.fk_ticket","=","tickets.id")
                ->join("vehicles as v","v.id","=","tickets.fk_vehicle")
                ->where("tickets.fk_vehicle","=",$vehicle->id)
                ->where("st.fk_settlement","=",$settlement->id)
                ->orderBy("tickets.date_gen","asc")
                ->get();

                $deductions_fee = DeductionVehiclesModel::select("deduction_vehicles.*")
                ->join("deductions", "deduction_vehicles.fk_deduction","=","deductions.id")
                ->join("settlements_deduction as sd","sd.fk_deduction","=","deductions.id")
                ->where("sd.fk_settlement","=",$settlement->id)
                ->where("deductions.fk_deduction_type","=","2")
                ->where("deduction_vehicles.fk_vehicle","=",$vehicle->id)
                ->get();   
                
                
                $arrInt = [
                    "tickets" => $tickets,
                    "deductions_fee" => $deductions_fee
                ];

                $data[$settlement->id]["vehicles"][$vehicle->id] = $arrInt;
            }

            $deductions_insurance = DeductionsModel::select(DB::raw("sum(sd.value) as sd_value"))
            ->join("settlements_deduction as sd","sd.fk_deduction","=","deductions.id")
            ->where("sd.fk_settlement","=",$settlement->id)
            ->where("deductions.fk_deduction_type","=","3")                
            ->first();

            $data[$settlement->id]["insurance"] = $deductions_insurance;

            $other_deductions = DeductionsModel::select(DB::raw("sum(sd.value) as sd_value"))
            ->join("settlements_deduction as sd","sd.fk_deduction","=","deductions.id")
            ->where("sd.fk_settlement","=",$settlement->id)
            ->where("deductions.fk_deduction_type","=","1")                
            ->first();

            $data[$settlement->id]["other_deductions"] = $other_deductions;
        }

        $config = ConfigModel::findOrFail(1);
        //dd($data);
        $html = view('pdf/payment_contractor',[
            "data" => $data,
            "contractor" => $contractor,
            "config" => $config
        ])->render();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html ,'UTF-8');
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();
        $dompdf->stream("Payment - ".$id_payment, array('compress' => 1, 'Attachment' => 0));
    }
}
