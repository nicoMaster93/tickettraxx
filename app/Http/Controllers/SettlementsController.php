<?php

namespace App\Http\Controllers;

use App\Mail\EnvioPdfsMail;

use App\Models\ContractorsModel;
use App\Models\DeductionsModel;
use App\Models\PaymentsModel;
use App\Models\SettlementsModel;
use App\Models\SettlementsTicketsModel;
use App\Models\TicketsModel;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Swift_Mailer;
use Swift_SmtpTransport;
use Illuminate\Support\Facades\Mail;


class SettlementsController extends Controller
{
    public function index(Request $request){        
        $today = new Datetime();
        $settlements = SettlementsModel::
        where("start_date","<=",$today->format('Y-m-d'))
        ->where("end_date",">=",$today->format('Y-m-d'))
        ->where("fk_settlement_state","=","1")
        ->whereNull("fk_payment")
        ->get();
        $settlements_out_date = SettlementsModel::
        where("end_date","<",$today->format('Y-m-d'))
        ->where("fk_settlement_state","=","1")
        ->whereNull("fk_payment")
        ->get();
        
        $settlements_upcoming = SettlementsModel:: 
        where("start_date",">",$today->format('Y-m-d'))
        ->where("fk_settlement_state","=","1")
        ->whereNull("fk_payment")
        ->get();
        return view('settlements/lista',[
            "settlements" => $settlements,
            "settlements_out_date" => $settlements_out_date,
            "settlements_upcoming" => $settlements_upcoming
        ]);
    }


    public function details($id){
        $settlements = SettlementsModel::findOrFail($id);
        
        //dd($settlements->settlements_deductions);

        $html = view('settlements/ajax/details', [
            "settlements_tickets" => $settlements->settlements_tickets,
            "settlements_deductions" => $settlements->settlements_deductions,
            "settlements_other_payments" => $settlements->settlements_other_payments
        ])->render();
        
        return response()->json([
            "success" => true,
            "html" => $html
        ]);
    }

    public function liquidate(Request $request){
        
        if(!$request->has("select-settlement")){
            
            return redirect()->route('settlements.index')->withErrors(['error' => 'Select at least one settlement']);
        }
        else{
            $arrSettlements = $request->input("select-settlement");
            $sumaTotal = SettlementsModel::select(DB::raw("sum(for_contractor) as suma"))
            ->whereIn("id", $arrSettlements)->first();
            
            $deductions = DeductionsModel::select("sd.*","deductions.*")
            ->join("settlements_deduction as sd","sd.fk_deduction","=","deductions.id")
            ->join("settlements","sd.fk_settlement","=","settlements.id")
            ->whereIn("deductions.fk_deduction_type",["1","2"])->get();
            foreach($deductions as $deduction){
                $deduction->balance_due = $deduction->balance_due - $deduction->value;
                if($deduction->balance_due == 0){
                    $deduction->fk_deduction_state = 3;
                }
                $deduction->save();
            }

            $payment = new PaymentsModel();
            $payment->date_pay = $request->input("payment_date_hidden");
            $payment->total = ($sumaTotal->suma ?? 0);
            $payment->save();
            
            SettlementsModel::whereIn("id", $arrSettlements)->update([
                "fk_payment" => $payment->id,
                "fk_settlement_state" => 3
            ]);

            TicketsModel::whereHas('settlement_ticket', function($query) use($arrSettlements) {
                $query->whereIn("settlements_tickets.fk_settlement",$arrSettlements);
            })->update([
                "fk_ticket_state" => "4"
            ]);

            $transport = new Swift_SmtpTransport("mail.web-html.com", 26, "TLS");
            $transport->setStreamOptions(array('ssl' => array('allow_self_signed' => true, 'verify_peer' => false, 'verify_peer_name' => false)));
            $transport->setUsername("concrete@web-html.com");
            $transport->setPassword("Mdc*1800*");
            $customSwiftMailer = new Swift_Mailer($transport);
            Mail::setSwiftMailer($customSwiftMailer);
            
            $contractors = ContractorsModel::select('contractors.*')
            ->join("settlements as s", "s.fk_contractor","contractors.id")
            ->whereIn("s.id", $arrSettlements)->get();

            foreach($contractors as $contractor){
                $pdf = PaymentsController::pdfMail($contractor->id, $payment->id);
                $asunto = "Payment - ".$payment->id;
                $html = "<h1>Payment - ".$payment->id."</h1><br><br>Another data";
                try{
                    //Mail::to($contractor->email)->send(new EnvioPdfsMail($asunto, $html, "concrete@web-html.com", "Concrete", $pdf));
                }
                 catch (\Exception $e) {
                    return response()->json([
                        "success" => false,
                        "error" => $e->getMessage()
                    ]);
                }
                
            }

            return redirect()->route('settlements.index')->with('message' , 'Payment successfully created');
        }

    }



}
