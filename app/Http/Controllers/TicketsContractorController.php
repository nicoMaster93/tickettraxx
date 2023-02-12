<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ticket\CreateRequest;
use App\Models\CustomerModel;
use App\Models\FscModel;
use App\Models\MaterialsModel;
use App\Models\PickupDeliverModel;
use App\Models\TicketsModel;
use App\Models\VehiclesModel;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

class TicketsContractorController extends Controller
{
    public function index(Request $request){

        $contractor = Auth::user()->contractor;
            
        $tickets = TicketsModel::whereHas('vehicle', function($query) use($contractor) {
            $query->where("vehicles.fk_contractor","=",$contractor->id);
        });
        $ticket_number = "";
        if($request->has("ticket_number") && !empty($request->input("ticket_number"))){
            $tickets = $tickets->where("tickets.number","like",$request->input("ticket_number")."%");
            $ticket_number = $request->input("ticket_number");
        }
        $start_date = "";
        if($request->has("start_date") && !empty($request->input("start_date"))){
            $tickets = $tickets->where("tickets.date_gen",">=",$request->input("start_date"));
            $start_date = $request->input("start_date");
        }
        $end_date = "";
        if($request->has("end_date") && !empty($request->input("end_date"))){
            $tickets = $tickets->where("tickets.date_gen","<=",$request->input("end_date"));
            $end_date = $request->input("end_date");
        }


        $tickets = $tickets->get();

        return view('tickets_contractor/lista', [
            "tickets" => $tickets,
            "ticket_number" => $ticket_number,
            "end_date" => $end_date,
            "start_date" => $start_date
        ]);
    }

    public function createForm(){        
        $contractor = Auth::user()->contractor;
        $materials = MaterialsModel::all();
        $vehicles = VehiclesModel::where("fk_contractor","=",$contractor->id)->where("fk_vehicle_state","=","1")->get();
        $pickups = PickupDeliverModel::where("type","=","0")->get();
        $delivers = PickupDeliverModel::where("type","=","1")->get();

        return view('tickets_contractor/create', [
            "materials" => $materials,
            "vehicles" => $vehicles,
            "pickups" => $pickups,
            "delivers" => $delivers
        ]);
    }

    public function create(CreateRequest $request){
        //Search vehicle by name in vehicles and aliases
        $contractor = Auth::user()->contractor;

        $vehicles = VehiclesModel::where("id","=",$request->vehicle)
        ->where("fk_contractor","=",$contractor->id)
        ->first();
        if(!isset($vehicles)){
            $vehicles = VehiclesModel::select("vehicles.id")
            ->join("vehicles_alias as va", "va.fk_vehicle", "vehicles.id")
            ->where("va.alias","=",$request->vehicle)
            ->where("vehicles.fk_contractor","=",$contractor->id)
            ->first();
            if(!isset($vehicles)){
                return Funciones::sendFailedResponse(["vehicle" => "Vehicle not found"]);
            }
        }

        if((!$request->hasFile("photo") && !$request->has("photo_box_data")) ||
            (!$request->hasFile("photo") && empty($request->photo_box_data))){
            return Funciones::sendFailedResponse(["photo_res" => "Picture ticket is required"]);
        }
       
        if($request->material == "other"){
            $material = new MaterialsModel();
            $material->name = $request->other_material;
            $material->save();
        }
        else{
            $material = MaterialsModel::findOrFail($request->material);
        }


        if($request->hasFile("photo")){
            //Agregar archivo por file   
            $folder = "public/ticket_files/";
            $file_name =  time()."_".$request->file("photo")->getClientOriginalName();
            $request->file("photo")->storeAs($folder, $file_name, "local");
        }
        else{
            //Agregar file por texto
            $folder = "public/ticket_files/";
            $file_name =  time()."_".$request->photo_box_name;
            Funciones::subirBase64($request->photo_box_data, $folder.$file_name);           
        }


        $pickup = PickupDeliverModel::find($request->pickup);
        $deliver = PickupDeliverModel::find($request->deliver);
        
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
        
        
        
        $date_pay = new DateTime($request->date_gen);
        $date_pay->add(new DateInterval("P14D"));
        $date_pay->modify('thursday this week');
        
        $ticket = new TicketsModel();
        $ticket->number = $request->number;
        $ticket->date_gen = $request->date_gen;
        $ticket->date_pay = $date_pay->format('Y-m-d');
        $ticket->pickup = $pickup->place;
        $ticket->deliver = $deliver->place;
        $ticket->file = $folder.$file_name;
        $ticket->tonage = $request->tonage;
        $ticket->rate = $request->rate;
        $ticket->total = $request->total;
        $ticket->fk_vehicle = $vehicles->id;
        $ticket->fk_material = $material->id;
        $ticket->fk_ticket_state = "1";
        if(isset($fsc)){
            $ticket->surcharge = ($request->total * ($fsc->percentaje /100) );
            $ticket->fk_surcharge = $fsc->id;
        }
        if(sizeof($customerPrefix) > 0){
            $customer = CustomerModel::where("prefix","=",$customerPrefix[0])->first();
            $ticket->fk_customer = ($customer->id ?? null);
        }
        $ticket->save();

        return redirect()->route('tickets_contractor.index')->with('message', 'Ticket created successfully!');
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
        $contractor = Auth::user()->contractor;

        foreach ($reader as $index => $row) {            
            try{
                foreach($row as $key =>$valor){
                    if($valor==""){
                        $row[$key]=null;
                    }
                    else{
                        $row[$key] = utf8_encode($row[$key]);
                    }
                }
                $vehicles = VehiclesModel::where("unit_number","=",$row[5])
                ->where("fk_contractor","=",$contractor->id)
                ->first();

                if(!isset($vehicles)){
                    $vehicles = VehiclesModel::select("vehicles.id")
                    ->join("vehicles_alias as va", "va.fk_vehicle", "vehicles.id")
                    ->where("va.alias","=",$row[5])
                    ->where("vehicles.fk_contractor","=",$contractor->id)
                    ->first();
                    if(!isset($vehicles)){
                        array_push($errors, "Vehicle not found on row ".($index+1));
                        continue;
                    }
                }
                
                $datePrev = explode("-",$row[1]);
                $date_gen = $datePrev[2]."-".$datePrev[0]."-".$datePrev[1];

                $date_pay = new DateTime($date_gen);
                $date_pay->add(new DateInterval("P14D"));
                $date_pay->modify('thursday this week');
                
                $ticket = new TicketsModel();
                $ticket->number = $row[0];
                $ticket->date_gen = $date_gen;
                $ticket->date_pay = $date_pay->format('Y-m-d');
                $ticket->pickup = ($row[7] ?? null);
                $ticket->deliver = ($row[8] ?? null);
                $ticket->tonage = $row[2];
                $ticket->rate = $row[3];
                $ticket->total = $row[4];
                $ticket->fk_vehicle = $vehicles->id;
                $ticket->fk_material = ($row[6] ?? null);
                $ticket->fk_ticket_state = "1";
                $ticket->save();

            }catch(Exception $e){
                array_push($errors, "Error ".$e->getMessage()." on row ".($index+1));
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

    public function editForm($id){        
        
        $ticket = TicketsModel::findOrFail($id);
        $contractor = Auth::user()->contractor;
        $materials = MaterialsModel::all();
        $vehicles = VehiclesModel::where("fk_contractor","=",$contractor->id)->where("fk_vehicle_state","=","1")->get();
        $pickups = PickupDeliverModel::where("type","=","0")->get();
        $delivers = PickupDeliverModel::where("type","=","1")->get();

        return view('tickets_contractor/edit', [
            "materials" => $materials,
            "ticket" => $ticket,
            "vehicles" => $vehicles,
            "pickups" => $pickups,
            "delivers" => $delivers
        ]);
    }


    public function update($id, CreateRequest $request){
        //Search vehicle by name in vehicles and aliases
        $contractor = Auth::user()->contractor;

        $vehicles = VehiclesModel::where("id","=",$request->vehicle)
        ->where("fk_contractor","=",$contractor->id)
        ->first();
        if(!isset($vehicles)){
            $vehicles = VehiclesModel::select("vehicles.id")
            ->join("vehicles_alias as va", "va.fk_vehicle", "vehicles.id")
            ->where("va.alias","=",$request->vehicle)
            ->where("vehicles.fk_contractor","=",$contractor->id)
            ->first();
            if(!isset($vehicles)){
                return Funciones::sendFailedResponse(["vehicle" => "Vehicle not found"]);
            }
        }

       
        if($request->material == "other"){
            $material = new MaterialsModel();
            $material->name = $request->other_material;
            $material->save();
        }
        else{
            $material = MaterialsModel::find($request->material);
        }


        


        
        $ticket = TicketsModel::findOrFail($id);
        $fileFinal = $ticket->file;
        if($request->hasFile("photo")){
            //Agregar archivo por file   
            if(isset($ticket->file) && !empty($ticket->file)){
                Storage::delete($ticket->file);
            }

            $folder = "public/ticket_files/";
            $file_name =  time()."_".$request->file("photo")->getClientOriginalName();
            $request->file("photo")->storeAs($folder, $file_name, "local");
            $fileFinal = $folder.$file_name;
        }
        else if($request->has("photo_box_data") && !empty($request->input("photo_box_data"))){
            //Agregar file por texto
            if(isset($ticket->file) && !empty($ticket->file)){
                Storage::delete($ticket->file);
            }

            $folder = "public/ticket_files/";
            $file_name =  time()."_".$request->photo_box_name;
            Funciones::subirBase64($request->photo_box_data, $folder.$file_name);  
            $fileFinal = $folder.$file_name;
        }

        
        $pickup = PickupDeliverModel::find($request->pickup);
        $deliver = PickupDeliverModel::find($request->deliver);
        
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
        
        
        $date_pay = new DateTime($request->date_gen);
        $date_pay->add(new DateInterval("P14D"));
        $date_pay->modify('thursday this week');
        
        $ticket->number = $request->number;
        $ticket->date_gen = $request->date_gen;
        $ticket->date_pay = $date_pay->format('Y-m-d');
        $ticket->pickup = $pickup->place;
        $ticket->deliver = $deliver->place;
        $ticket->file = $fileFinal;
        $ticket->tonage = $request->tonage;
        $ticket->rate = $request->rate;
        $ticket->total = $request->total;
        $ticket->fk_vehicle = $vehicles->id;
        $ticket->fk_material = ($material->id ?? null);
        $ticket->fk_ticket_state = "1";
        $ticket->return_message = "";
        if(isset($fsc)){
            $ticket->surcharge = ($request->total * ($fsc->percentaje /100) );
            $ticket->fk_surcharge = $fsc->id;
        }
        if(sizeof($customerPrefix) > 0){
            $customer = CustomerModel::where("prefix","=",$customerPrefix[0])->first();
            $ticket->fk_customer = ($customer->id ?? null);
        }
        $ticket->save();

        return redirect()->route('tickets_contractor.index')->with('message', 'Ticket updated successfully!');
    }
    

}
