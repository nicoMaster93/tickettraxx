<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ticket\CreateRequest;
use App\Mail\EnvioNormalMail;
use App\Models\ConfigModel;
use App\Models\ContractorsModel;
use App\Models\CustomerModel;
use App\Models\DeductionsModel;
use App\Models\FscModel;
use App\Models\MaterialsModel;
use App\Models\OtherPaymentsModel;
use App\Models\PickupDeliverModel;
use App\Models\PO_CodesModel;
use App\Models\SettlementsDeductionsModel;
use App\Models\SettlementsModel;
use App\Models\SettlementsOtherPaymentsModel;
use App\Models\SettlementsTicketsModel;
use App\Models\TicketsModel;
use App\Models\VehiclesModel;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;
use Swift_Mailer;
use Swift_SmtpTransport;

use function Symfony\Component\String\b;

class TicketsController extends Controller
{
    private $quickbooks;

    private $arrCambios = [
        "$" => "",
        "," => ".",
        "USD / 1 TON" => ""
    ];
    public function __construct(){
        $this->quickbooks = new QuickBooksController();
    }
    
    public function index(){
        $user = Auth::user();
        if($user->fk_rol == 1){
            return view('tickets/dashboard');
        }
        else{
            
            if($user->contractor->fk_contractor_state == "1"){
                return redirect(route("tickets_contractor.index"));
            }
            else{
                return view('layouts.appContractorDisabled');
            }
            
        }
        
    }

    public function to_verify(Request $request){
        $tickets = TicketsModel::whereIn("fk_ticket_state",["1", "2"]);
        if($request->has("start_date")){
            $tickets->where("date_gen", ">=", $request->start_date);
        }
        if($request->has("end_date")){
            $tickets->where("date_gen", "<=", $request->end_date);
        }
        $tickets = $tickets->get();

        return view('tickets/to_verify',[
            "tickets" => $tickets,
            "start_date" => $request->start_date,
            "end_date" => $request->end_date
        ]);
        
    }
    
    public function info($ticketId){
        $ticket = TicketsModel::findOrFail($ticketId);
        if(isset($ticket->file) && !empty($ticket->file)){
            $url = Storage::url($ticket->file);
            $type = Storage::mimeType($ticket->file);
            $image = "";
            if(strpos($type, 'image') !== false){
                $data = Storage::get($ticket->file);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                $image = $base64;
            }
            $ticket->file_url = $url;
            $ticket->image_base_64 = $image;
        }

        $ticket->date_gen = date("m/d/Y",strtotime($ticket->date_gen));
        $ticket->rate = "$".number_format($ticket->rate,2);
        $ticket->total = "$".number_format($ticket->total,2);
        $ticket->ticket_state;
        $ticket->material;
        $ticket->vehicle;
        $ticket->recheck_link = route('tickets.recheck', ['id'=>$ticketId]);
        


        return response()->json([
            "success" => true,
            "ticket" => $ticket
        ]);

    }
    public function delete_ticket($ticketId){
        try {
            $ticket = TicketsModel::findOrFail($ticketId);
            $ticket->record_status = 0;
            $del = $ticket->save();
            return response()->json([
                "success" => (($del) ? true : false),
                "message" => (($del) ? "Ticket deleted successfully" : "The Ticket could not be deleted")
            ]);
        } catch (\Throwable $th) {
        }

    }

    public function recheck($ticketId){
        $ticket = TicketsModel::find($ticketId);
        $ticket->fk_ticket_state = "1";
        $ticket->save();


        $settlement_ticket = SettlementsTicketsModel::where("fk_ticket", "=", $ticket->id)->first();   
        $forContractor = $ticket->total * ($ticket->vehicle->contractor->percentage/100);

        $settlement_ticket->settlement->subtotal = $settlement_ticket->settlement->subtotal - $ticket->total;
        $settlement_ticket->settlement->total = $settlement_ticket->settlement->total - $ticket->total;
        $settlement_ticket->settlement->for_contractor = $settlement_ticket->settlement->for_contractor - $forContractor;
        $settlement_ticket->settlement->save();

        if($settlement_ticket->settlement->total == 0){
            $settlement_deductions = SettlementsDeductionsModel::where("fk_settlement","=", $settlement_ticket->fk_settlement)->get();
            foreach($settlement_deductions as  $settlement_deduction){                
                if($settlement_deduction->deduction->fk_deduction_type == "1" || $settlement_deduction->deduction->fk_deduction_type == "2"){
                    $settlement_deduction->deduction->fk_deduction_state = 1; 
                }
                $settlement_deduction->deduction->save();
            }

            $settlement_other_payments = SettlementsOtherPaymentsModel::where("fk_settlement","=", $settlement_ticket->fk_settlement)->get();
            foreach($settlement_other_payments as  $settlement_other_payment){                
                $settlement_other_payment->other_payments->fk_other_payment_state = 1; 
                $settlement_other_payment->other_payments->save();
            }

            $settlement_ticket->settlement->delete();
        }
        
        $settlement_ticket->delete();



        return redirect()->route('tickets.to_verify')->with('message', 'Ticket sent to check!');;
    }

    public function change_state(Request $request){
        $ticket = TicketsModel::find($request->id);
        $ticket->fk_ticket_state = $request->state;
        if($request->has("message") && !empty($request->message)){
            $ticket->return_message = $request->message;
        }        
        $ticket->save();

        if($ticket->fk_ticket_state == "2"){
            $transport = new Swift_SmtpTransport("mail.web-html.com", 26, "TLS");
            $transport->setStreamOptions(array('ssl' => array('allow_self_signed' => true, 'verify_peer' => false, 'verify_peer_name' => false)));
            $transport->setUsername("concrete@web-html.com");
            $transport->setPassword("Mdc*1800*");
        
            $customSwiftMailer = new Swift_Mailer($transport);
            Mail::setSwiftMailer($customSwiftMailer);
            
            $asunto = "Ticket rejected";
            $html = "<h1>Ticket ".$ticket->number."</h1><br><br><p>The ticket ".$ticket->number." was rejected by: ".$ticket->return_message.", please verify in panel</p>";

            $contractor = ContractorsModel::findOrFail($ticket->vehicle->fk_contractor);

            try{
                Mail::to($contractor->email)->send(new EnvioNormalMail($asunto, $html, "concrete@web-html.com", "Concrete"));
            }
                catch (\Exception $e) {
                return response()->json([
                    "success" => false,
                    "error" => $e->getMessage()
                ]);
            }
        }
        
        if($ticket->fk_ticket_state == "3"){
            $inicio_settlement = new DateTime($ticket->date_pay);
            $inicio_settlement->modify('monday this week');

            $fin_settlement = new DateTime($ticket->date_pay);
            $fin_settlement->modify('saturday this week');

            $settlement = SettlementsModel::where("start_date", "=", $inicio_settlement->format('Y-m-d'))
            ->where("end_date", "=", $fin_settlement->format('Y-m-d'))
            ->where("fk_contractor", "=", $ticket->vehicle->fk_contractor)
            ->where("fk_settlement_state", "=", "1")
            ->first();

            if(!isset($settlement)){
                $settlement = new SettlementsModel();
                $settlement->start_date = $inicio_settlement->format('Y-m-d');
                $settlement->end_date = $fin_settlement->format('Y-m-d');
                $settlement->subtotal = $ticket->total;
                $settlement->deduction = 0;
                $settlement->other_payments = 0;
                $settlement->total = $ticket->total;
                $settlement->for_contractor = round($ticket->total * ($ticket->vehicle->contractor->percentage/100), 2);
                $settlement->fk_contractor = $ticket->vehicle->fk_contractor;
                $settlement->fk_settlement_state = "1";
                $settlement->save();
                
                $settlement_ticket = new SettlementsTicketsModel();
                $settlement_ticket->fk_ticket = $ticket->id;
                $settlement_ticket->fk_settlement = $settlement->id;
                $settlement_ticket->save();
    
                //Verificamos si hay deducciones pendientes por agregar
                $deductions = DeductionsModel::whereRaw("fk_deduction_state = 1 or fk_deduction_type = 3")
                ->where("date_pay","<",$settlement->end_date)->get();
                foreach($deductions as $deduction){
                    $deductionValue = 0;
                    if($deduction->fk_deduction_type == "3"){
                        $vehicles = VehiclesModel::select(DB::raw("count(*) as cuenta"))
                        ->where("fk_contractor","=", $deduction->fk_contractor)
                        ->where("fk_vehicle_state","=","1")
                        ->first();
                        $config = ConfigModel::findOrFail(1);
                        $deductionValue = $config->insurance * ($vehicles->cuenta ?? 0);
                        $settlement->deduction = $settlement->deduction + $deductionValue;
                        $settlement->for_contractor = $settlement->for_contractor - $deductionValue;

                    }
                    else if($deduction->fk_deduction_type == "2"){
                        $deductionValue = $deduction->total_value;
                        $settlement->deduction = $settlement->deduction + $deduction->total_value;
                        $settlement->for_contractor = $settlement->for_contractor - $deduction->total_value;
                    }
                    else if($deduction->fk_deduction_type == "1"){
                        if(isset($deduction->number_installments)){
                            $deductionValue = round($deduction->total_value/$deduction->number_installments,2);
                        }
                        else{
                            $deductionValue = $deduction->fixed_value;
                        }
                        
                        if($deductionValue > $deduction->balance_due){
                            $deductionValue = $deduction->balance_due;
                        }
                        $settlement->deduction = $settlement->deduction + $deductionValue;
                        $settlement->for_contractor = $settlement->for_contractor - $deductionValue;
                    }
                    $settlement->save();

                    $settlement_deduction = new SettlementsDeductionsModel();            
                    $settlement_deduction->value = $deductionValue;
                    $settlement_deduction->fk_deduction = $deduction->id;
                    $settlement_deduction->fk_settlement = $settlement->id;
                    $settlement_deduction->save();
                    $deduction->fk_deduction_state = 2;
                    $deduction->save();
                }

                //Verificamos si hay otros pagos pendientes por agregar
                $other_payments = OtherPaymentsModel::where("fk_other_payment_state","=","1")
                ->whereBetween("date_pay",[$inicio_settlement->format('Y-m-d'), $fin_settlement->format('Y-m-d')])
                ->get();
                foreach($other_payments as $other_payment){
                    $settlement->other_payments = $settlement->other_payments + $other_payment->total;
                    $settlement->for_contractor = $settlement->for_contractor + $other_payment->total;
                    $settlement->save();

                    $setttlement_payment = new SettlementsOtherPaymentsModel();
                    $setttlement_payment->fk_other_payments = $other_payment->id;
                    $setttlement_payment->fk_settlement = $settlement->id;
                    $setttlement_payment->save();


                    $other_payment->fk_other_payment_state = 2; //Added to settlement
                    $other_payment->save();
                }

            }
            else{
                $settlement->subtotal = $settlement->subtotal + $ticket->total;
                $settlement->deduction = 0;
                $settlement->other_payments = 0;
                $settlement->total = $settlement->total + $ticket->total;
                $settlement->for_contractor = $settlement->for_contractor + round($ticket->total * ($ticket->vehicle->contractor->percentage/100), 2);
                $settlement->save();

                $settlement_ticket = new SettlementsTicketsModel();
                $settlement_ticket->fk_ticket = $ticket->id;
                $settlement_ticket->fk_settlement = $settlement->id;
                $settlement_ticket->save();
              
            }
        }
        

        return redirect()->route('tickets.to_verify')->with('message', 'Ticket changed successfully!');;
    }

    public function search_list(Request $request){
        $tickets = TicketsModel::whereIn("fk_ticket_state",["3"]);
        /* Agrego estado */
        $tickets->where("record_status", ">", 0);
        if($request->has("start_date")){
            $tickets->where("date_gen", ">=", $request->start_date);
        }
        if($request->has("end_date")){
            $tickets->where("date_gen", "<=", $request->end_date);
        }
        $tickets = $tickets->get();

        return view('tickets/search',[
            "tickets" => $tickets,
            "start_date" => $request->start_date,
            "end_date" => $request->end_date
        ]);
        
    }

    public function createForm($ticketId=0){      
        $materials = MaterialsModel::all();
        $contractors = ContractorsModel::orderBy("company_name")->get();
        $vehicles = array();
        $errors = Session::get('errors');
        if(isset($errors)){
            $olds = Session::getOldInput();
            $vehicles = VehiclesModel::where("fk_contractor","=",$olds['contractor'])->where("fk_vehicle_state","=","1")->get();
        }
        $pickups = PickupDeliverModel::where("type","=","0")->get();
        $delivers = PickupDeliverModel::where("type","=","1")->get();
        $response = [
            "materials" => $materials,
            "contractors" => $contractors,
            "vehicles" => $vehicles,
            "pickups" => $pickups,
            "delivers" => $delivers
        ];
        if($ticketId > 0){
            /* Viene de edicion */
            $response['dataTicket'] = TicketsModel::find($ticketId);
            return view('tickets/update', $response);
        }else{
            return view('tickets/create', $response);
        }
    }

    public function create(CreateRequest $request){
        //Search vehicle by name in vehicles and aliases
        
        $vehicles = VehiclesModel::where("unit_number","=",$request->vehicle)->orWhere("id","=",$request->vehicle)->first();
        if(!isset($vehicles)){
            $vehicles = VehiclesModel::select("vehicles.id")
            ->join("vehicles_alias as va", "va.fk_vehicle", "vehicles.id")
            ->where("va.alias","=",$request->vehicle)->first();
            if(!isset($vehicles)){
                return Funciones::sendFailedResponse(["vehicle" => "Vehicle not found"]);
            }
        }
        if(!$request->has("tikcetId")){
            if((!$request->hasFile("photo") && !$request->has("photo_box_data")) ||
                (!$request->hasFile("photo") && empty($request->photo_box_data))){
                return Funciones::sendFailedResponse(["photo_res" => "Picture ticket is required"]);
            }
        }
       
        if($request->material == "other"){
            $material = new MaterialsModel();
            $material->name = $request->other_material;
            $material->save();
        }
        else{
            $material = MaterialsModel::findOrFail($request->material);
        }
        // dd($request->all());
        if($request->hasFile("photo")){
            //Agregar archivo por file   
            $folder = "public/ticket_files/";
            $file_name =  time()."_".$request->file("photo")->getClientOriginalName();
            $request->file("photo")->storeAs($folder, $file_name, "local");
        }
        else{
            //Agregar file por texto
            if(!is_null($request->photo_box_data)){
                $folder = "public/ticket_files/";
                $file_name =  time()."_".$request->photo_box_name;
                Funciones::subirBase64($request->photo_box_data, $folder.$file_name);           
            }
        }
        
        
        
        $pickup = PickupDeliverModel::find($request->pickup);
        $deliver = PickupDeliverModel::find($request->deliver);
        Log::info(['$pickup', $request->all()]);
        
        
        $date_pay = new DateTime($request->date_gen);
        $date_pay->add(new DateInterval("P14D"));
        $date_pay->modify('thursday this week');

        
        
        /*if($date_pay->format('N') == "6" || $date_pay->format('N') == "7"){
            $date_pay->modify('thursday next week');
        }
        else{
            $date_pay->modify('thursday this week');
        }*/
        
        //Find FSC
        $customerPrefix = explode("-",$deliver->place);
        
        if(sizeof($customerPrefix) > 0){
            $fsc = FscModel::select("surcharge.*")
            ->join("customer","customer.id","=","surcharge.fk_customer")
            ->where("from","<=",$request->date_gen)
            ->where("to",">=",$request->date_gen)
            ->where("customer.prefix","=",$customerPrefix[0])
            ->first();
        }
        
        if(!$request->has("tikcetId")){
            /* Insert */
            $ticket = new TicketsModel();
        }else{
            /* Update */
            $ticket = TicketsModel::find($request->tikcetId);
        }


        $ticket->number = $request->number;
        $ticket->date_gen = $request->date_gen;
        $ticket->date_pay = $date_pay->format('Y-m-d');
        $ticket->pickup = $pickup->place;
        $ticket->deliver = $deliver->place;
        if(isset($folder)){
            $ticket->file = $folder.$file_name;
        }
        $ticket->tonage = $request->tonage;
        $ticket->rate = $request->rate;
        $ticket->total = $request->total;
        $ticket->fk_vehicle = $vehicles->id;
        $ticket->fk_material = $material->id;
        $ticket->fk_ticket_state =  ($ticket->fk_ticket_state ?? "3");
        if(isset($fsc)){
            $ticket->surcharge = ($request->total * ($fsc->percentaje /100) );
            $ticket->fk_surcharge = $fsc->id;
        }
        if(sizeof($customerPrefix) > 0){
            $customer = CustomerModel::where("prefix","=",$customerPrefix[0])->first();
            $ticket->fk_customer = ($customer->id ?? null);
        }
        $ticket->save();

        /*
        * JN || This part will be updated only if the status is not "to check [ 1 ] "
        */
        if($ticket->fk_ticket_state != 1){

            $inicio_settlement = new DateTime($ticket->date_pay);
            $inicio_settlement->modify('monday this week');
    
            $fin_settlement = new DateTime($ticket->date_pay);
            $fin_settlement->modify('saturday this week');
    
            $settlement = SettlementsModel::where("start_date", "=", $inicio_settlement->format('Y-m-d'))
            ->where("end_date", "=", $fin_settlement->format('Y-m-d'))
            ->where("fk_contractor", "=", $vehicles->fk_contractor)
            ->where("fk_settlement_state", "=", "1")
            ->first();
    
            if(!isset($settlement)){
                $settlement = new SettlementsModel();
                $settlement->start_date = $inicio_settlement->format('Y-m-d');
                $settlement->end_date = $fin_settlement->format('Y-m-d');
                $settlement->subtotal = $ticket->total;
                $settlement->deduction = 0;
                $settlement->other_payments = 0;
                $settlement->total = $ticket->total;
                $settlement->surcharge = $ticket->surcharge;
                $settlement->for_contractor = round($ticket->total * ($vehicles->contractor->percentage/100), 2);
                $settlement->fk_contractor = $vehicles->fk_contractor;
                $settlement->fk_settlement_state = "1";
                $settlement->save();
                
                $settlement_ticket = new SettlementsTicketsModel();
                $settlement_ticket->fk_ticket = $ticket->id;
                $settlement_ticket->fk_settlement = $settlement->id;
                $settlement_ticket->save();
    
                //Verificamos si hay deducciones pendientes por agregar
                $deductions = DeductionsModel::whereRaw("fk_deduction_state = 1 or fk_deduction_type = 3")
                ->where("date_pay","<",$settlement->end_date)->get();
                foreach($deductions as $deduction){
                    $deductionValue = 0;
                    if($deduction->fk_deduction_type == "3"){
                        $vehicles = VehiclesModel::select(DB::raw("count(*) as cuenta"))
                        ->where("fk_contractor","=", $deduction->fk_contractor)
                        ->where("fk_vehicle_state","=","1")
                        ->first();
                        $config = ConfigModel::findOrFail(1);
                        if(isset($vehicles)){
                            $deductionValue = $config->insurance * ($vehicles->cuenta ?? 0);
                        }                    
                        $settlement->deduction = $settlement->deduction + $deductionValue;
                        $settlement->for_contractor = $settlement->for_contractor - $deductionValue;
    
                    }
                    else if($deduction->fk_deduction_type == "2"){
                        $deductionValue = $deduction->total_value;
                        $settlement->deduction = $settlement->deduction + $deduction->total_value;
                        $settlement->for_contractor = $settlement->for_contractor - $deduction->total_value;
                    }
                    else if($deduction->fk_deduction_type == "1"){
                        if(isset($deduction->number_installments)){
                            $deductionValue = round($deduction->total_value/$deduction->number_installments,2);
                        }
                        else{
                            $deductionValue = $deduction->fixed_value;
                        }
                        
                        if($deductionValue > $deduction->balance_due){
                            $deductionValue = $deduction->balance_due;
                        }
                        $settlement->deduction = $settlement->deduction + $deductionValue;
                        $settlement->for_contractor = $settlement->for_contractor - $deductionValue;
                    }
                    
                    $settlement->save();
    
                    $settlement_deduction = new SettlementsDeductionsModel();            
                    $settlement_deduction->value = $deductionValue;
                    $settlement_deduction->fk_deduction = $deduction->id;
                    $settlement_deduction->fk_settlement = $settlement->id;
                    $settlement_deduction->save();
                    $deduction->fk_deduction_state = 2;
                    $deduction->save();
                }
    
                //Verificamos si hay otros pagos pendientes por agregar
                $other_payments = OtherPaymentsModel::where("fk_other_payment_state","=","1")
                ->whereBetween("date_pay",[$inicio_settlement->format('Y-m-d'), $fin_settlement->format('Y-m-d')])
                ->get();
                foreach($other_payments as $other_payment){
                    $settlement->other_payments = $settlement->other_payments + $other_payment->total;
                    $settlement->for_contractor = $settlement->for_contractor + $other_payment->total;
                    $settlement->save();
    
                    $setttlement_payment = new SettlementsOtherPaymentsModel();
                    $setttlement_payment->fk_other_payments = $other_payment->id;
                    $setttlement_payment->fk_settlement = $settlement->id;
                    $setttlement_payment->save();
    
    
                    $other_payment->fk_other_payment_state = 2; //Added to settlement
                    $other_payment->save();
                }
            }
            else{
    
                $settlement->subtotal = $settlement->subtotal + $ticket->total;
                $settlement->deduction = 0;
                $settlement->other_payments = 0;
                $settlement->total = $settlement->total + $ticket->total;
                $settlement->surcharge = $settlement->surcharge + $ticket->surcharge;
                $settlement->for_contractor = $settlement->for_contractor + round($ticket->total * ($vehicles->contractor->percentage/100), 2);
                $settlement->save();
    
                $settlement_ticket = new SettlementsTicketsModel();
                $settlement_ticket->fk_ticket = $ticket->id;
                $settlement_ticket->fk_settlement = $settlement->id;
                $settlement_ticket->save();
            }
            
            if($request->has("tikcetId")){
                return redirect()->route('tickets.search_list')->with('message', 'Ticket update successfully!');
            }
            return redirect()->route('tickets.search_list')->with('message', 'Ticket created successfully!');

        }else{
            return Funciones::closeWindow('Ticket update successfully!');
        }


        
    }

    public function upload(Request $request){
        if(!$request->has("file64")){
            return response()->json([
                "success" => false,
                "message" => "File don't found!"
            ]);
        }
        if(empty($request->file64)){
            return response()->json([
                "success" => false,
                "message" => "File don't found!"
            ]);
        }
        $errors = array();
        $folder = "public/ticket_files/";
        $file_name =  time()."_.csv";
        Funciones::subirBase64($request->file64, $folder.$file_name);      
        $contents = Storage::get($folder.$file_name);
            
        $reader = Reader::createFromString($contents);
        $reader->setDelimiter(';');
        foreach ($reader as $index => $row) {            
            try{
                if($index == 0 && strtolower($row[0])!=="date"){
                    /* Validamos que tenga las cabeceras */
                    return response()->json([
                        "success" => false,
                        "message" => "The file doesn't contain headers!"
                    ]);
                }else if($index == 0){
                    /* Si tiene cabeceras las omitimos y continuamos con la data */
                    continue;
                }

                foreach($row as $key =>$valor){
                    if($valor==""){
                        $row[$key]=null;
                    }
                    else{
                        $row[$key] = trim(utf8_encode($row[$key]));
                    }
                }

                $vehicles = VehiclesModel::where("unit_number","=",$row[2])->first();
                if(!isset($vehicles)){
                    $vehicles = VehiclesModel::select("vehicles.id")
                    ->join("vehicles_alias as va", "va.fk_vehicle", "vehicles.id")
                    ->where("va.alias","=",$row[2])->first();
                    if(!isset($vehicles)){
                        array_push($errors, "Vehicle not found on row ".($index+1));
                        continue;
                    }
                }
                
                $datePrev = explode("/",$row[0]);
                $date_gen = $datePrev[2]."-".$datePrev[0]."-".$datePrev[1];

                $date_pay = new DateTime($date_gen);
                $date_pay->add(new DateInterval("P14D"));
                $date_pay->modify('thursday this week');
                
   
                $row[7] = $this->corregir_data($row[7]);//Rate
                $row[6] = $this->corregir_data($row[6]);//Tonage                
                $row[8] = $this->corregir_data($row[8]);//Total
                $row[9] = $this->corregir_data($row[9]);//FSC


                $ticket = new TicketsModel();
                $ticket->number = $row[1];
                $ticket->date_gen = $date_gen;
                $ticket->date_pay = $date_pay->format('Y-m-d');
                $ticket->pickup = ($row[3] ?? null);
                $ticket->deliver = ($row[4] ?? null);
                $ticket->tonage = $row[6];
                $ticket->rate = $row[7];
                $ticket->total = $row[8];
                $ticket->surcharge = ($row[9] ?? 0);
                $ticket->fk_vehicle = $vehicles->id;
                $ticket->fk_material = ($row[5] ?? null);
                $ticket->fk_ticket_state = "3";
                //Find FSC
                $customerPrefix = explode("-",$row[4]);                
                if(sizeof($customerPrefix) > 0){
                    $customer = CustomerModel::where("prefix","=",$customerPrefix[0])->first();
                    $ticket->fk_customer = ($customer->id ?? null);
                }
                $ticket->save();

                $inicio_settlement = new DateTime($ticket->date_pay);
                $inicio_settlement->modify('monday this week');

                $fin_settlement = new DateTime($ticket->date_pay);
                $fin_settlement->modify('saturday this week');
                
                $settlement = SettlementsModel::where("start_date", "=", $inicio_settlement->format('Y-m-d'))
                ->where("end_date", "=", $fin_settlement->format('Y-m-d'))
                ->where("fk_contractor", "=", $vehicles->fk_contractor)
                ->where("fk_settlement_state", "=", "1")
                ->first();

                if(!isset($settlement)){                    
                    $settlement = new SettlementsModel();
                    $settlement->start_date = $inicio_settlement->format('Y-m-d');
                    $settlement->end_date = $fin_settlement->format('Y-m-d');
                    $settlement->subtotal = $ticket->total;
                    $settlement->deduction = 0;
                    $settlement->other_payments = 0;
                    $settlement->total = $ticket->total;
                    $settlement->for_contractor = round($ticket->total * ($vehicles->contractor->percentage/100), 2);
                    $settlement->surcharge = $ticket->surcharge;
                    //array_push($errors, $settlement->for_contractor);
                    $settlement->fk_contractor = $vehicles->fk_contractor;
                    $settlement->fk_settlement_state = "1";
                    $settlement->save();
                    
                    $settlement_ticket = new SettlementsTicketsModel();
                    $settlement_ticket->fk_ticket = $ticket->id;
                    $settlement_ticket->fk_settlement = $settlement->id;
                    $settlement_ticket->save();
                    //Verificamos si hay deducciones pendientes por agregar
                    $deductions = DeductionsModel::where("fk_deduction_state","=","1")
                    ->where("date_pay","<",$settlement->end_date)->get();
                    foreach($deductions as $deduction){
                        $deductionValue = 0;
                        if($deduction->fk_deduction_type == "3"){
                            $vehicles = VehiclesModel::select(DB::raw("count(*) as cuenta"))
                            ->where("fk_contractor","=", $deduction->fk_contractor)
                            ->where("fk_vehicle_state","=","1")
                            ->first();
                            $config = ConfigModel::findOrFail(1);
                            $deductionValue = $config->insurance * ($vehicles->cuenta ?? 0);
                            $settlement->deduction = $settlement->deduction + $deductionValue;
                            $settlement->for_contractor = $settlement->for_contractor - $deductionValue;

                        }
                        else if($deduction->fk_deduction_type == "2"){
                            $deductionValue = $deduction->total_value;
                            $settlement->deduction = $settlement->deduction + $deduction->total_value;
                            $settlement->for_contractor = $settlement->for_contractor - $deduction->total_value;
                        }
                        else if($deduction->fk_deduction_type == "1"){
                            if(isset($deduction->number_installments)){
                                $deductionValue = round($deduction->total_value/$deduction->number_installments,2);
                            }
                            else{
                                $deductionValue = $deduction->fixed_value;
                            }
                            
                            if($deductionValue > $deduction->balance_due){
                                $deductionValue = $deduction->balance_due;
                            }
                            $settlement->deduction = $settlement->deduction + $deductionValue;
                            $settlement->for_contractor = $settlement->for_contractor - $deductionValue;
                        }                        
                        $settlement->save();

                        
                        $settlement_deduction = new SettlementsDeductionsModel();            
                        $settlement_deduction->value = $deductionValue;
                        $settlement_deduction->fk_deduction = $deduction->id;
                        $settlement_deduction->fk_settlement = $settlement->id;
                        $settlement_deduction->save();
                        $deduction->fk_deduction_state = 2;
                        $deduction->save();
                    }
                    //Verificamos si hay otros pagos pendientes por agregar
                    $other_payments = OtherPaymentsModel::where("fk_other_payment_state","=","1")
                    ->whereBetween("date_pay",[$inicio_settlement->format('Y-m-d'), $fin_settlement->format('Y-m-d')])
                    ->get();
                    foreach($other_payments as $other_payment){
                        $settlement->other_payments = $settlement->other_payments + $other_payment->total;
                        $settlement->for_contractor = $settlement->for_contractor + $other_payment->total;
                        $settlement->save();

                        $setttlement_payment = new SettlementsOtherPaymentsModel();
                        $setttlement_payment->fk_other_payments = $other_payment->id;
                        $setttlement_payment->fk_settlement = $settlement->id;
                        $setttlement_payment->save();


                        $other_payment->fk_other_payment_state = 2; //Added to settlement
                        $other_payment->save();
                    }



                }
                else{
                    $for_round = round($ticket->total * ($vehicles->contractor->percentage/100), 2);
                    $settlement->subtotal = $settlement->subtotal + $ticket->total;
                    $settlement->total = $settlement->total + $ticket->total;
                    $settlement->for_contractor = $settlement->for_contractor + $for_round;
                    $settlement->surcharge = $settlement->surcharge + $ticket->surcharge;
                    //array_push($errors, $for_round." ".$settlement->for_contractor);
                    $settlement->save();

                    $settlement_ticket = new SettlementsTicketsModel();
                    $settlement_ticket->fk_ticket = $ticket->id;
                    $settlement_ticket->fk_settlement = $settlement->id;
                    $settlement_ticket->save();
                   

                }

            }catch(Exception $e){
                array_push($errors, "Error ".$e->getMessage().", line: ".$e->getLine()." on row ".($index+1));
            }            
        }

        if(sizeof($errors)>0){
            return response()->json([
                "success" => false,
                "message" => implode("<br>", $errors)
            ]);
        }
        else{
            return response()->json([
                "success" => true,
                "message" => "File uploaded successfully!"
            ]);
        }
        
    }

    public function corregir_data($string){
        foreach($this->arrCambios as $cambio => $valor){
            $string = trim(str_replace($cambio, $valor, $string));
        }
        return $string;
    }

    public function createInvoice(Request $request){        
        $tickets = TicketsModel::whereIn("fk_ticket_state",["3","4"])->where("isInvoiced","=","0");
        if($request->has("start_date")){
            $tickets->where("date_gen", ">=", $request->start_date);
        }
        if($request->has("end_date")){
            $tickets->where("date_gen", "<=", $request->end_date);
        }
        $tickets = $tickets->get();
        $authUrl = $this->quickbooks->getAuthUrl();

        return view('tickets/createInvoice',[
            "tickets" => $tickets,
            "start_date" => $request->start_date,
            "end_date" => $request->end_date,
            "authUrl" => $authUrl
        ]);

    }

    public function createFormInvoice(Request $request){
        if(!$request->has("select-ticket")){
            return response()->json([
                "success" => false,
                "message" => "Please select a ticket"
            ]);
        }
        if(!session()->has('sessionAccessToken')){
            return response()->json([
                "success" => false,
                "message" => "Please login to quickbooks"
            ]);
        }
        
        $cuentaCustomer = TicketsModel::select("tickets.fk_customer")
        ->whereIn("tickets.id",$request->input("select-ticket"))->groupBy("tickets.fk_customer")->get();
        
        if(sizeof($cuentaCustomer) > 1){
            return response()->json([
                "success" => false,
                "message" => "Tickets must only be from the same customer"
            ]);
        }

        $cuentaPickUp = TicketsModel::select(DB::raw("tickets.pickup"))
        ->whereIn("tickets.id",$request->input("select-ticket"))
        ->groupBy("tickets.pickup")
        ->get();


        if(sizeof($cuentaPickUp) > 1){
            return response()->json([
                "success" => false,
                "message" => "Tickets must have the same pickup"
            ]);
        }
        
        $cuentaDeliver = TicketsModel::select(DB::raw("count(deliver) as cuenta"))->groupBy("deliver")
        ->whereIn("tickets.id",$request->input("select-ticket"))->get();
        if(sizeof($cuentaDeliver) > 1){
            return response()->json([
                "success" => false,
                "message" => "Tickets must have the same deliver"
            ]);
        }

        $customer = CustomerModel::select(DB::raw("customer.*"))
        ->join("tickets","tickets.fk_customer","=","customer.id")
        ->whereIn("tickets.id",$request->input("select-ticket"))
        ->first();

        $tickets = TicketsModel::whereIn("tickets.id",$request->input("select-ticket"))->get();

        
        if(!isset($customer->id_quickbooks)){
            return response()->json([
                "success" => false,
                "message" => "The customer does not have an associated quickbooks id"
            ]);
        }
        
        $po = PO_CodesModel::join("pickup_deliver as pickup","pickup.id","=","p_o_codes.fk_pickup")
        ->join("pickup_deliver as deliver","deliver.id","=","p_o_codes.fk_deliver")
        ->where("deliver.place","=",$tickets[0]->deliver)
        ->where("pickup.place","=",$tickets[0]->pickup)
        ->first();

        $items = $this->quickbooks->getItems();
        if(!$items["success"]){
            return response()->json([
                "success" => false,
                "message" => $items["message"]
            ]);
        }
        
        $surcharges = [];
        foreach($tickets as $ticket){
            if($ticket->surcharge > 0){
                $fsc = FscModel::select("surcharge.*")
                ->where("from","<=",$ticket->date_gen)
                ->where("to",">=",$ticket->date_gen)
                ->where("fk_customer","=",$ticket->fk_customer)
                ->first();
                if(isset($fsc)){
                    $percentaje = $fsc->percentaje;
                }
                else{
                    $percentaje = "-";
                }
                $surcharges[$percentaje] = (isset($surcharges[$percentaje]) ? ($surcharges[$percentaje] + $ticket->surcharge) : $ticket->surcharge);
            }
        }

        

        return response()->json([
            "success" => true,
            "form" => view('tickets.createFormInvoice',[
                "customer" => $customer,
                "tickets" => $tickets,
                "surcharges" => $surcharges,
                "po" => $po,
                "items" => $items["items"]
            ])->render()
        ]);



    }

    public function change_field(Request $request){
        try {
            //code...
            print_r("ENtro");die;
        } catch (\Throwable $th) {
            dd($th);
        }
        // Log::info("entro en la peticion post");
        
        // // $ticket = TicketsModel::find($request->id);
        // // $ticket->fk_ticket_state = $request->state;
        // // if($request->has("message") && !empty($request->message)){
        // //     $ticket->return_message = $request->message;
        // // }        
        // // $ticket->save();

        // return response()->json([ "success" => true ]);
    }
}
