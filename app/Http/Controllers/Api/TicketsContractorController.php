<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Funciones;
use App\Http\Requests\Ticket\CreateRequest;
use App\Models\CustomerModel;
use App\Models\FscModel;
use App\Models\MaterialsModel;
use App\Models\PickupDeliverModel;
use App\Models\PO_CodesModel;
use App\Models\TicketsModel;
use App\Models\VehiclesModel;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TicketsContractorController extends Controller
{
    /**
     * Collection of tickets
     * Shows all tickets by contractor
     * 
	 * @group  v 1.0.0
     * 
     * @bodyParam start_date String optional Start date for ticket filter.
     * @bodyParam end_date String optional End date for ticket filter.
     * @bodyParam ticket_number String optional Ticket number to find.
     * 
     * @authenticated
     * 
	 */
    public function show(Request $request){
        //Get contractor by user
        $contractor = auth()->user()->contractor;
        //Get tickets with filter
        $tickets = TicketsModel::select(["tickets.id","number","date_gen", DB::raw("DATE_FORMAT(date_gen,'%d/%m/%Y') as date_gen_f"), "vehicles.unit_number", "pickup", 
        "deliver", "tonage", "rate", "total","fk_ticket_state",
        "fk_material",DB::raw("REPLACE(file, 'public/', 'storage/') as file"),"return_message"])
        ->join("vehicles","tickets.fk_vehicle","=","vehicles.id")
        ->where("vehicles.fk_contractor","=",$contractor->id);
        if($request->has("ticket_number") && !empty($request->input("ticket_number"))){
            $tickets = $tickets->where("tickets.number","like",$request->input("ticket_number")."%");
        }
        if($request->has("start_date") && !empty($request->input("start_date"))){
            $tickets = $tickets->where("tickets.date_gen",">=",$request->input("start_date"));
        }
        if($request->has("end_date") && !empty($request->input("end_date"))){
            $tickets = $tickets->where("tickets.date_gen","<=",$request->input("end_date"));
        }
        $tickets = $tickets->orderBy("tickets.date_gen","desc");

        //Page and number of records by consult
        $page = 0;
        $records = 6;
        if($request->has("page")){
            $page = $request->input("page");
        }        
        $tickets = $tickets->skip($page*$records)->take($records)->get();

        return response()->json([
            "success" => true,
            "message" => "Tickets showed successfully",
            "data" => [
                "tickets" => $tickets,
                "current_page" => $page
            ]                    
        ], 200);
    }


    /**
     * Detail of ticket
     * Shows all tickets by contractor
     * 
	 * @group  v 1.0.0
     * 
     * @bodyParam id Integer required Id of ticket.
     * 
     * @authenticated
     * 
	 */
    public function detail($id){
        //Get contractor by user
        $contractor = auth()->user()->contractor;
        //Get tickets with filter
        $ticket = TicketsModel::select(
        [
            "tickets.id",
            "number",
            "date_gen", 
            DB::raw("DATE_FORMAT(date_gen,'%d/%m/%Y') as date_gen_f"), 
            "vehicles.unit_number", 
            "pickup", 
            "deliver",
            "tonage",
            "rate",
            "total",
            "fk_ticket_state",
            DB::raw("materials.name as material"),
            DB::raw("REPLACE(file, 'public/', 'storage/') as file"),
            "return_message",
            DB::raw("ticket_states.name as state"),
        ])
        ->join("vehicles","tickets.fk_vehicle","=","vehicles.id")
        ->join("materials","tickets.fk_material","=","materials.id", "left")
        ->join("ticket_states","tickets.fk_ticket_state","=","ticket_states.id", "left")        
        ->where("vehicles.fk_contractor","=",$contractor->id)
        ->where("tickets.id","=",$id)
        ->first();
        
        $ticket->rate = "$ ".number_format($ticket->rate, 2);
        $ticket->total = "$ ".number_format($ticket->total, 2);
        

        if(isset($ticket)){
            return response()->json([
                "success" => true,
                "message" => "Ticket showed successfully",
                "ticket" => $ticket 
            ], 200);
        }
        else{
            return response()->json([
                "success" => false,
                "message" => "Ticket does not belong to this contractor",
                "ticket" => $ticket         
            ], 200);
        }
        

        
    }


    /**
     * Create ticket
     * Create a ticket to verify
     * 
	 * @group  v 1.0.0
     * 
     * @bodyParam number String required Ticket Number
     * @bodyParam date_gen Date required Ticket generation date format YYYY-MM-DD
     * @bodyParam vehicle String required Unit number or Alias of vechile
     * @bodyParam material String Material id or with value "other"
     * @bodyParam other_material String Required if material is "other"
     * @bodyParam pickup String Ticket Pickup
     * @bodyParam deliver String Ticket Deliver
     * @bodyParam tonage Decimal required Ticket Tonage
     * @bodyParam rate Decimal required Ticket Rate
     * @bodyParam total Decimal required Ticket Total by default is rate multiplied by tonage
     * @bodyParam photo File Ticket photo in file format
     * @bodyParam photo_box_data String Ticket photo in base64 
     * @bodyParam photo_box_name String Ticket photo name, if it sended by base64
     * 
     * @authenticated
     * 
	 */  
    
    public function create(CreateRequest $request){
        
        $contractor = auth()->user()->contractor;
        //Search vehicle by name in vehicles and aliases
        $vehicles = VehiclesModel::where("unit_number","=",$request->vehicle)
        ->where("fk_contractor","=",$contractor->id)
        ->first();
        if(!isset($vehicles)){
            $vehicles = VehiclesModel::select("vehicles.id")
            ->join("vehicles_alias as va", "va.fk_vehicle", "vehicles.id")
            ->where("va.alias","=",$request->vehicle)
            ->where("vehicles.fk_contractor","=",$contractor->id)
            ->first();
            if(!isset($vehicles)){
                return response()->json([
                    "success" => false,
                    "error" => "Vehicle not found"
                ], 422);
            }
        }

        if((!$request->hasFile("photo") && !$request->has("photo_box_data")) ||
            (!$request->hasFile("photo") && empty($request->photo_box_data))){
                return response()->json([
                    "success" => false,
                    "error" => "Picture ticket is required"
                ], 422);
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
            //Agregar archivo por texto base 64
            $folder = "public/ticket_files/";
            $file_name =  time()."_".$request->photo_box_name;
            Funciones::subirBase64($request->photo_box_data, $folder.$file_name);           
        }
        
        $pickup = PickupDeliverModel::where("place","=",$request->pickup)->first();
        $deliver = PickupDeliverModel::where("place","=",$request->deliver)->first();
        
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

        return response()->json([
            "success" => true,
            "message" => "Ticket added successfully",
            "data" => [
                "tickets" => $ticket
            ]                    
        ], 200);
    }


    /**
     * Update ticket
     * Update ticket if state is sended to recheck or to verify
     * 
	 * @group  v 1.0.0
     * 
     * @urlParam  id Integer required ID of ticket to modify.
     * 
     * @bodyParam number String required Ticket Number
     * @bodyParam date_gen Date required Ticket generation date format YYYY-MM-DD
     * @bodyParam vehicle String required Unit number or Alias of vechile
     * @bodyParam material String Material id or with value "other"
     * @bodyParam other_material String Required if material is "other"
     * @bodyParam pickup String Ticket Pickup
     * @bodyParam deliver String Ticket Deliver
     * @bodyParam tonage Decimal required Ticket Tonage
     * @bodyParam rate Decimal required Ticket Rate
     * @bodyParam total Decimal required Ticket Total by default is rate multiplied by tonage
     * @bodyParam photo File Ticket photo in file format
     * @bodyParam photo_box_data String Ticket photo in base64 
     * @bodyParam photo_box_name String Ticket photo name, if it sended by base64
     * 
     * @authenticated
     * 
	 */  

    public function update($id, CreateRequest $request){
        //Search vehicle by name in vehicles and aliases
        $contractor = auth()->user()->contractor;

        $vehicles = VehiclesModel::where("unit_number","=",$request->vehicle)
        ->where("fk_contractor","=",$contractor->id)
        ->first();
        if(!isset($vehicles)){
            $vehicles = VehiclesModel::select("vehicles.id")
            ->join("vehicles_alias as va", "va.fk_vehicle", "vehicles.id")
            ->where("va.alias","=",$request->vehicle)
            ->where("vehicles.fk_contractor","=",$contractor->id)
            ->first();
            if(!isset($vehicles)){
                return response()->json([
                    "success" => false,
                    "error" => "Vehicle not found"
                ], 422);
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
        
        
        $date_pay = new DateTime($request->date_gen);
        $date_pay->add(new DateInterval("P14D"));
        $date_pay->modify('thursday this week');        
        
        $ticket->number = $request->number;
        $ticket->date_gen = $request->date_gen;
        $ticket->date_pay = $date_pay->format('Y-m-d');
        $ticket->pickup = $request->pickup;
        $ticket->deliver = $request->deliver;
        $ticket->file = $fileFinal;
        $ticket->tonage = $request->tonage;
        $ticket->rate = $request->rate;
        $ticket->total = $request->total;
        $ticket->fk_vehicle = $vehicles->id;
        $ticket->fk_material = ($material->id ?? null);
        $ticket->fk_ticket_state = "1";
        $ticket->return_message = "";
        $ticket->save();

        return response()->json([
            "success" => true,
            "message" => "Ticket updated successfully!",
            "data" => [
                "tickets" => $ticket
            ]                    
        ], 200);
    }

    /**
     * Get Materials
     * Shows all materials
     * 
	 * @group  v 1.0.0
     * 
     * 
     * 
	 */
    public function materials(){
        $materials = MaterialsModel::all();

        return response()->json([
            "success" => true,
            "message" => "Materials showed successfully!",
            "data" => [
                "materials" => $materials
            ]
        ], 200);
    }


    /**
     * Get PickUp or Delivery
     * Shows all PickUp or Delivery
     * 
	 * @group  v 1.0.0
     * 
     * 
     * @urlParam type String required PickUp or Delivery.
     * 
	 */
    public function pickup_deliver($type){
        $pickup_deliver = PickupDeliverModel::where("type","=",$type)->get();
        return response()->json([
            "success" => true,
            "message" => "Pickup Deliver showed successfully!",
            "data" => [
                "pickup_deliver" => $pickup_deliver
            ]                    
        ], 200);
    }


    /**
     * Get Vehicles by contractor
     * Shows all Vehicles by contractor
     * 
	 * @group  v 1.0.0
     * 
     * 
     * @authenticated
     * 
	 */
    public function vehicles_by_contractor(){
        
        $contractor = auth()->user()->contractor;

        $vehicles = VehiclesModel::where("fk_contractor","=",$contractor->id)
        ->where("fk_vehicle_state", "=", "1")
        ->get();

        return response()->json([
            "success" => true,
            "message" => "Vehicles showed successfully!",
            "data" => [
                "vehicles" => $vehicles
            ]                    
        ], 200);
    }

     /**
     * Get rate by pickup and deliver
     * Get rate by pickup and deliver
     * 
	 * @group  v 1.0.0
     * 
     * @urlParam pickup String required pickup to consult.
     * @urlParam deliver String required deliver to consult.
     * 
     * @authenticated
     * 
	 */
    public function rate_by_pickup_deliver($pickup, $deliver){
        
        $po = PO_CodesModel::join("pickup_deliver as pickup", "pickup.id", "=", "p_o_codes.fk_pickup")
        ->join("pickup_deliver as deliver", "deliver.id", "=", "p_o_codes.fk_deliver")
        ->where("pickup.place","=",$pickup)
        ->where("deliver.place","=",$deliver)
        ->first();

        return response()->json([
            "success" => true,
            "message" => "Rate showed successfully!",
            "data" => [
                "po" => $po
            ]                    
        ], 200);
    }

    /**
     * Get FSC by deliver and date
     * 
	 * @group  v 1.0.0
     * 
     * @urlParam deliver String required deliver to consult.
     * @urlParam date String required date to consult.
     * 
     * @authenticated
     * 
	 */
    public function fsc_by_deliver_date($deliver, $date){
        
        
        //Find FSC
        
       $customerPrefix = explode("-",$deliver);
    
        if(sizeof($customerPrefix) > 0){
            $fsc = FscModel::select("surcharge.*")
            ->join("customer","customer.id","=","surcharge.fk_customer")
            ->where("from","<=",$date)
            ->where("to",">=",$date)
            ->where("customer.prefix","=",$customerPrefix[0])
            ->first();
                
            
        }

        return response()->json([
            "success" => true,
            "message" => "Fsc showed successfully!",
            "data" => [
                "fsc" => ($fsc ?? null)
            ]                    
        ], 200); 
        
        
        
    }

   
        
}
