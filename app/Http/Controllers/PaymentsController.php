<?php

namespace App\Http\Controllers;

use App\Mail\EnvioPdfsMail;
use App\Models\ConfigModel;
use Swift_Mailer;
use Swift_SmtpTransport;
use Illuminate\Support\Facades\Mail;
use App\Models\ContractorsModel;
use App\Models\DeductionsModel;
use App\Models\DeductionVehiclesModel;
use App\Models\OtherPaymentsModel;
use App\Models\PaymentsModel;
use App\Models\SettlementsModel;
use App\Models\TicketsModel;
use App\Models\VehiclesModel;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request){
        
        $start = date("Y-m-01");
        $final = date("Y-m-t");
        if($request->has("start_date")) $start = $request->input("start_date");
        if($request->has("end_date")) $final = $request->input("end_date");

        $settlements = SettlementsModel::
        select("fk_contractor","fk_payment", "fk_settlement_state", DB::raw('sum(for_contractor) as total'),  DB::raw('sum(surcharge) as surcharge'))
        ->whereHas('payment', function($query) use($start, $final) {
            $query->where("payments.date_pay",">=",$start)
            ->where("payments.date_pay", "<=",$final);
        })
        ->groupBy( "fk_contractor", "fk_payment", "fk_settlement_state")
        ->get();
    
        
        return view('payments/lista', [
            "settlements" => $settlements,
            "start_date" => $start,
            "end_date" => $final
        ]);

    }

    public function details($id_contractor, $id_payment){
        
        $settlements = SettlementsModel::
        where("fk_contractor", "=", $id_contractor)
        ->where("fk_payment", "=", $id_payment)
        ->get();

        return view('payments/listaDetails', [
            "settlements" => $settlements
        ]);
    }

    public function reconciliation(Request $request){
        $conciliacion = DB::table("contractors", "c")
        ->selectRaw('c.company_name, (SELECT count(v.id) from vehicles as v WHERE v.fk_contractor = c.id) as cuenta_truck, 
        (SELECT v.unit_number from vehicles as v WHERE v.fk_contractor = c.id LIMIT 1) as primer_vehiculo,
        (SELECT sum(s.subtotal) FROM settlements as s WHERE s.fk_contractor = c.id and s.fk_payment in(SELECT p.id from payments as p WHERE p.date_pay BETWEEN "'.$request->start_date_reconciliation.'" and "'.$request->end_date_reconciliation.'")) as gross_payment,
        c.percentage,
        (SELECT sum(sd.value) from settlements_deduction as sd WHERE sd.fk_deduction in(SELECT d.id from deductions as d WHERE d.fk_deduction_type = "3") and sd.fk_settlement in (SELECT s.id FROM settlements as s WHERE s.fk_contractor = c.id and s.fk_payment in(SELECT p.id from payments as p WHERE p.date_pay BETWEEN "'.$request->start_date_reconciliation.'" and "'.$request->end_date_reconciliation.'"))) as insurance,
        (SELECT sum(sd.value) from settlements_deduction as sd WHERE sd.fk_deduction in(SELECT d.id from deductions as d WHERE d.fk_deduction_type = "1") and sd.fk_settlement in (SELECT s.id FROM settlements as s WHERE s.fk_contractor = c.id and s.fk_payment in(SELECT p.id from payments as p WHERE p.date_pay BETWEEN "'.$request->start_date_reconciliation.'" and "'.$request->end_date_reconciliation.'"))) as loans,
        (SELECT sum(sd.value) from settlements_deduction as sd WHERE sd.fk_deduction in(SELECT d.id from deductions as d WHERE d.fk_deduction_type = "2") and sd.fk_settlement in (SELECT s.id FROM settlements as s WHERE s.fk_contractor = c.id and s.fk_payment in(SELECT p.id from payments as p WHERE p.date_pay BETWEEN "'.$request->start_date_reconciliation.'" and "'.$request->end_date_reconciliation.'"))) as fuel,
        (SELECT sum(s.other_payments) FROM settlements as s WHERE s.fk_contractor = c.id and s.fk_payment in(SELECT p.id from payments as p WHERE p.date_pay BETWEEN "'.$request->start_date_reconciliation.'" and "'.$request->end_date_reconciliation.'")) as ot_payments,
        (SELECT sum(s.total) FROM settlements as s WHERE s.fk_contractor = c.id and s.fk_payment in(SELECT p.id from payments as p WHERE p.date_pay BETWEEN "'.$request->start_date_reconciliation.'" and "'.$request->end_date_reconciliation.'")) as net_pay')->get();

        $html = view('pdf/reconciliation',[
            "conciliacion" => $conciliacion,
            "start_date" => $request->start_date_reconciliation,
            "end_date" => $request->end_date_reconciliation 
        ])->render();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html ,'UTF-8');
        $dompdf->setPaper('letter', 'landscape');
        $dompdf->render();
        $dompdf->stream("Reconciliation", array('compress' => 1, 'Attachment' => 0));

    }
    

    
    public function pdf($id_contractor, $id_payment){

        $contractor = ContractorsModel::findOrFail($id_contractor);

        $settlements = SettlementsModel::where("fk_payment","=",$id_payment)
        ->where("fk_contractor","=",$id_contractor)
        ->get();
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
        $dompdf->setPaper('letter', 'landscape');
        $dompdf->render();
        $dompdf->stream("Payment - ".$id_payment, array('compress' => 1, 'Attachment' => 0));
    }

    public static function pdfMail($id_contractor, $id_payment){

        $contractor = ContractorsModel::findOrFail($id_contractor);

        $settlements = SettlementsModel::where("fk_payment","=",$id_payment)->get();
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
       
        
        $html = view('pdf/payment_contractor',[
            "data" => $data,
            "contractor" => $contractor,
            "config" => $config
        ])->render();

        $dompdf = new Dompdf();

        //$dompdf = new Dompdf();
        

        $dompdf->loadHtml($html ,'UTF-8');
        $dompdf->setPaper('letter', 'landscape');
        $dompdf->render();
        //$dompdf->stream("Payment - ".$id_payment, array('compress' => 1, 'Attachment' => 0));
        $pdf = $dompdf->output();
        return $pdf;     
    }

    public function sendPdfMail($id_contractor, $id_payment){
        $transport = new Swift_SmtpTransport("mail.web-html.com", 26, "TLS");
        $transport->setStreamOptions(array('ssl' => array('allow_self_signed' => true, 'verify_peer' => false, 'verify_peer_name' => false)));
        $transport->setUsername("concrete@web-html.com");
        $transport->setPassword("Mdc*1800*");
      
        $customSwiftMailer = new Swift_Mailer($transport);
        Mail::setSwiftMailer($customSwiftMailer);
        $pdf = PaymentsController::pdfMail($id_contractor, $id_payment);
        $asunto = "Payment - ".$id_payment;
        $html = "<h1>Payment - ".$id_payment."</h1><br><br>Another data";

        $contractor = ContractorsModel::findOrFail($id_contractor);

        try{
            //Mail::to($contractor->email)->send(new EnvioPdfsMail($asunto, $html, "concrete@web-html.com", "Concrete", $pdf));
        }
            catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "error" => $e->getMessage()
            ]);
        }

        return redirect()->route('payments.index')->with('message' , 'Email has been sent');
        
    }
}
