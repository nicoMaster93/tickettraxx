<?php

namespace App\Http\Controllers;

use App\Http\Requests\Vehicle\CreateRequest;
use App\Models\ContractorsModel;
use App\Models\DriversModel;
use App\Models\VehicleAliasModel;
use App\Models\VehicleDriversModel;
use App\Models\VehiclesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehiclesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($idContractor){
        
        $vehicles = VehiclesModel::where("fk_contractor", $idContractor)->where("fk_vehicle_state", "1")->get();
        $contractor = ContractorsModel::where("id",$idContractor)->first();
        $drivers = DriversModel::where("fk_contractor", $idContractor)->where("fk_driver_state", "1")->get();

       return view('vehicles/lista', [
            "vehicles" => $vehicles,
            "contractor" => $contractor,
            "drivers" => $drivers
        ]);        
    }

    public function addDriver(Request $request){
        
        $vehicle_driver =  new VehicleDriversModel();
        $vehicle_driver->fk_vehicle = $request->vehicle_id;
        $vehicle_driver->fk_driver = $request->driver;
        $vehicle_driver->save();  
        $vehicle = VehiclesModel::find($request->vehicle_id);
        return redirect()->route('vehicles.index', ["id" => $vehicle->fk_contractor])->with('message', 'Driver has been successfully assigned');
    }

    public function createForm($idContractor){        
        $contractor = ContractorsModel::where("id",$idContractor)->first();
        return view('vehicles/create', [
            "contractor" => $contractor
        ]);
    }

    public function create($idContractor, CreateRequest $request){
                
        /*if((!$request->hasFile("photo_truck_dot_inspection") && !$request->has("photo_truck_dot_inspection_box_data")) ||
            (!$request->hasFile("photo_truck_dot_inspection") && empty($request->photo_truck_dot_inspection_box_data))){
            return Funciones::sendFailedResponse(["photo_truck_dot_inspection_res" => "Picture truck DOT inspection is required"]);
        }
        if((!$request->hasFile("photo_truck_registration") && !$request->has("photo_truck_registration_box_data")) ||
            (!$request->hasFile("photo_truck_registration") && empty($request->photo_truck_registration_box_data))){
            return Funciones::sendFailedResponse(["photo_truck_registration_res" => "Picture of truck registration is required"]);
        }
        if((!$request->hasFile("photo_trailer_dot_inspection") && !$request->has("photo_trailer_dot_inspection_box_data")) ||
            (!$request->hasFile("photo_trailer_dot_inspection") && empty($request->photo_trailer_dot_inspection_box_data))){
            return Funciones::sendFailedResponse(["photo_trailer_dot_inspection_res" => "Picture trailer DOT inspection is required"]);
        }
        if((!$request->hasFile("photo_trailer_registration") && !$request->has("photo_trailer_registration_box_data")) ||
            (!$request->hasFile("photo_trailer_registration") && empty($request->photo_trailer_registration_box_data))){
            return Funciones::sendFailedResponse(["photo_trailer_registration_res" => "Picture of trailer registration is required"]);
        }
        if((!$request->hasFile("photo_trailer_over") && !$request->has("photo_trailer_over_box_data")) ||
            (!$request->hasFile("photo_trailer_over") && empty($request->photo_trailer_over_box_data))){
            return Funciones::sendFailedResponse(["photo_trailer_over_res" => "Picture of trailer Overweight Permit is required"]);
        }*/


        $folder = "public/vehicles_files/";
        if($request->hasFile("photo_truck_dot_inspection")){
            $file_truck_dot_inspection_name =  uniqid()."_".$request->file("photo_truck_dot_inspection")->getClientOriginalName();
            $request->file("photo_truck_dot_inspection")->storeAs($folder, $file_truck_dot_inspection_name, "local");
        }
        else if($request->has("photo_truck_dot_inspection_box_data") && !empty($request->input("photo_truck_dot_inspection_box_data"))) {
            $file_truck_dot_inspection_name =  uniqid()."_".$request->photo_truck_dot_inspection_box_name;
            Funciones::subirBase64($request->photo_truck_dot_inspection_box_data, $folder.$file_truck_dot_inspection_name);           
        }

        if($request->hasFile("photo_truck_registration")){
            $file_truck_registration_name =  uniqid()."_".$request->file("photo_truck_registration")->getClientOriginalName();
            $request->file("photo_truck_registration")->storeAs($folder, $file_truck_registration_name, "local");
        }
        else if($request->has("photo_truck_registration_box_data") && !empty($request->input("photo_truck_registration_box_data"))) {
            $file_truck_registration_name =  uniqid()."_".$request->photo_truck_registration_box_name;
            Funciones::subirBase64($request->photo_truck_registration_box_data, $folder.$file_truck_registration_name);           
        }
        
        if($request->hasFile("photo_trailer_dot_inspection")){
            $file_trailer_dot_inspection_name =  uniqid()."_".$request->file("photo_trailer_dot_inspection")->getClientOriginalName();
            $request->file("photo_trailer_dot_inspection")->storeAs($folder, $file_trailer_dot_inspection_name, "local");
        }
        else if($request->has("photo_trailer_dot_inspection_box_data") && !empty($request->input("photo_trailer_dot_inspection_box_data"))) {
            $file_trailer_dot_inspection_name =  uniqid()."_".$request->photo_trailer_dot_inspection_box_name;
            Funciones::subirBase64($request->photo_trailer_dot_inspection_box_data, $folder.$file_trailer_dot_inspection_name);           
        }

        if($request->hasFile("photo_trailer_registration")){
            $file_trailer_registration_name =  uniqid()."_".$request->file("photo_trailer_registration")->getClientOriginalName();
            $request->file("photo_trailer_registration")->storeAs($folder, $file_trailer_registration_name, "local");
        }
        else if($request->has("photo_trailer_registration_box_data") && !empty($request->input("photo_trailer_registration_box_data"))) {
            $file_trailer_registration_name =  uniqid()."_".$request->photo_trailer_registration_box_name;
            Funciones::subirBase64($request->photo_trailer_registration_box_data, $folder.$file_trailer_registration_name);           
        }

        if($request->hasFile("photo_trailer_over")){
            $file_trailer_over_name =  uniqid()."_".$request->file("photo_trailer_over")->getClientOriginalName();
            $request->file("photo_trailer_over")->storeAs($folder, $file_trailer_over_name, "local");
        }
        else if($request->has("photo_trailer_over_box_data") && !empty($request->input("photo_trailer_over_box_data"))) {
            $file_trailer_over_name =  uniqid()."_".$request->photo_trailer_over_box_name;
            Funciones::subirBase64($request->photo_trailer_over_box_data, $folder.$file_trailer_over_name);           
        }



        $vehicle = new VehiclesModel();
        $vehicle->unit_number = $request->unit_number;
        $vehicle->truck_model_brand = $request->truck_model_brand;
        $vehicle->truck_year = $request->truck_year;
        $vehicle->truck_vin_number = $request->truck_vin_number;
        $vehicle->trailer_model_brand = $request->trailer_model_brand;
        $vehicle->trailer_year = $request->trailer_year;
        $vehicle->trailer_vin_number = $request->trailer_vin_number;
        $vehicle->fk_contractor = $idContractor;
        $vehicle->fk_vehicle_state = 1;
        $vehicle->photo_truck_dot_inspection = (isset($file_truck_dot_inspection_name) ? $folder.$file_truck_dot_inspection_name : null);
        $vehicle->photo_truck_registration =  (isset($file_truck_registration_name) ? $folder.$file_truck_registration_name : null);
        $vehicle->photo_trailer_dot_inspection = (isset($file_trailer_dot_inspection_name) ? $folder.$file_trailer_dot_inspection_name : null);
        $vehicle->photo_trailer_registration = (isset($file_trailer_registration_name) ? $folder.$file_trailer_registration_name : null);
        $vehicle->photo_trailer_over = (isset($file_trailer_over_name) ? $folder.$file_trailer_over_name : null);
        $vehicle->save();

        for($i=1; $i <= $request->num_alias; $i++) { 
            if($request->has("alias_" . $i)){
                VehicleAliasModel::insert([
                    "alias" => $request->input("alias_" . $i),
                    "fk_vehicle" => $vehicle->id
                ]);                
            } 
        }



        return redirect()->route('vehicles.index',["id" => $idContractor])->with('message', 'Vehicle created successfully!');
    }

    public function delete($id){
        
        $vehicle = VehiclesModel::findOrFail($id);
        $vehicle->fk_vehicle_state = 2;
        $vehicle->save();
        $idContractor = $vehicle->fk_contractor;
        
        return redirect()->route('vehicles.index',["id" => $idContractor])->with('message', 'Vehicle deleted successfully!');
    }

    public function editForm($id){
        
        $vehicle = VehiclesModel::findOrFail($id);
        $contractor = ContractorsModel::findOrFail($vehicle->fk_contractor);
        
        
        return view('vehicles/edit', [
            "vehicle" => $vehicle,
            "contractor" => $contractor
        ]);
    }

    public function update($id, CreateRequest $request){
                
        $vehicle = VehiclesModel::findOrFail($id);
        $vehicle->unit_number = $request->unit_number;
        $vehicle->truck_model_brand = $request->truck_model_brand;
        $vehicle->truck_year = $request->truck_year;
        $vehicle->truck_vin_number = $request->truck_vin_number;
        $vehicle->trailer_model_brand = $request->trailer_model_brand;
        $vehicle->trailer_year = $request->trailer_year;
        $vehicle->trailer_vin_number = $request->trailer_vin_number;
        $vehicle->fk_vehicle_state = 1;

        $folder = "public/vehicles_files/";

        if ($request->hasFile('photo_truck_dot_inspection')) {
            if(isset($vehicle->photo_truck_dot_inspection) && !empty($vehicle->photo_truck_dot_inspection)){
                Storage::delete($vehicle->photo_truck_dot_inspection);
            }
            $file_truck_dot_inspection_name =  uniqid()."_".$request->file("photo_truck_dot_inspection")->getClientOriginalName();
            $request->file("photo_truck_dot_inspection")->storeAs($folder, $file_truck_dot_inspection_name, "local");
            $vehicle->photo_truck_dot_inspection = $folder.$file_truck_dot_inspection_name;
        }
        else if($request->has("photo_truck_dot_inspection_box_data") && !empty($request->input("photo_truck_dot_inspection_box_data"))) {
            if(isset($vehicle->photo_truck_dot_inspection) && !empty($vehicle->photo_truck_dot_inspection)){
                Storage::delete($vehicle->photo_truck_dot_inspection);
            }
            $file_truck_dot_inspection_name =  uniqid()."_".$request->photo_truck_dot_inspection_box_name;
            Funciones::subirBase64($request->photo_truck_dot_inspection_box_data, $folder.$file_truck_dot_inspection_name);           
            $vehicle->photo_truck_dot_inspection = $folder.$file_truck_dot_inspection_name;
        }

        if ($request->hasFile('photo_truck_registration')) {
            if(isset($vehicle->photo_truck_registration) && !empty($vehicle->photo_truck_registration)){
                Storage::delete($vehicle->photo_truck_registration);
            }
            $file_truck_registration_name =  uniqid()."_".$request->file("photo_truck_registration")->getClientOriginalName();
            $request->file("photo_truck_registration")->storeAs($folder, $file_truck_registration_name, "local");
            $vehicle->photo_truck_registration = $folder.$file_truck_registration_name;
        }
        else if($request->has("photo_truck_registration_box_data") && !empty($request->input("photo_truck_registration_box_data"))) {
            if(isset($vehicle->photo_truck_registration) && !empty($vehicle->photo_truck_registration)){
                Storage::delete($vehicle->photo_truck_registration);
            }
            $file_truck_registration_name =  uniqid()."_".$request->photo_truck_registration_box_name;
            Funciones::subirBase64($request->photo_truck_registration_box_data, $folder.$file_truck_registration_name);           
            $vehicle->photo_truck_registration = $folder.$file_truck_registration_name;
        }

        if ($request->hasFile('photo_trailer_dot_inspection')) {
            if(isset($vehicle->photo_trailer_dot_inspection) && !empty($vehicle->photo_trailer_dot_inspection)){
                Storage::delete($vehicle->photo_trailer_dot_inspection);
            }
            $file_trailer_dot_inspection_name =  uniqid()."_".$request->file("photo_trailer_dot_inspection")->getClientOriginalName();
            $request->file("photo_trailer_dot_inspection")->storeAs($folder, $file_trailer_dot_inspection_name, "local");
            $vehicle->photo_trailer_dot_inspection = $folder.$file_trailer_dot_inspection_name;
        }
        else if($request->has("photo_trailer_dot_inspection_box_data") && !empty($request->input("photo_trailer_dot_inspection_box_data"))) {
            if(isset($vehicle->photo_trailer_dot_inspection) && !empty($vehicle->photo_trailer_dot_inspection)){
                Storage::delete($vehicle->photo_trailer_dot_inspection);
            }
            $file_trailer_dot_inspection_name =  uniqid()."_".$request->photo_trailer_dot_inspection_box_name;
            Funciones::subirBase64($request->photo_trailer_dot_inspection_box_data, $folder.$file_trailer_dot_inspection_name);           
            $vehicle->photo_trailer_dot_inspection = $folder.$file_trailer_dot_inspection_name;
        }

        if ($request->hasFile('photo_trailer_registration')) {
            if(isset($vehicle->photo_trailer_registration) && !empty($vehicle->photo_trailer_registration)){
                Storage::delete($vehicle->photo_trailer_registration);
            }
            $file_trailer_registration_name =  uniqid()."_".$request->file("photo_trailer_registration")->getClientOriginalName();
            $request->file("photo_trailer_registration")->storeAs($folder, $file_trailer_registration_name, "local");
            $vehicle->photo_trailer_registration = $folder.$file_trailer_registration_name;
        }
        else if($request->has("photo_trailer_registration_box_data") && !empty($request->input("photo_trailer_registration_box_data"))) {
            if(isset($vehicle->photo_trailer_registration) && !empty($vehicle->photo_trailer_registration)){
                Storage::delete($vehicle->photo_trailer_registration);
            }
            $file_trailer_registration_name =  uniqid()."_".$request->photo_trailer_registration_box_name;
            Funciones::subirBase64($request->photo_trailer_registration_box_data, $folder.$file_trailer_registration_name);           
            $vehicle->photo_trailer_registration = $folder.$file_trailer_registration_name;
        }

        if ($request->hasFile('photo_trailer_over')) {
            if(isset($vehicle->photo_trailer_over) && !empty($vehicle->photo_trailer_over)){
                Storage::delete($vehicle->photo_trailer_over);
            }
            $file_trailer_over_name =  uniqid()."_".$request->file("photo_trailer_over")->getClientOriginalName();
            $request->file("photo_trailer_over")->storeAs($folder, $file_trailer_over_name, "local");
            $vehicle->photo_trailer_over = $folder.$file_trailer_over_name;
        }
        else if($request->has("photo_trailer_over_box_data") && !empty($request->input("photo_trailer_over_box_data"))) {
            if(isset($vehicle->photo_trailer_over) && !empty($vehicle->photo_trailer_over)){
                Storage::delete($vehicle->photo_trailer_over);
            }
            $file_trailer_over_name =  uniqid()."_".$request->photo_trailer_over_box_name;
            Funciones::subirBase64($request->photo_trailer_over_box_data, $folder.$file_trailer_over_name);           
            $vehicle->photo_trailer_over = $folder.$file_trailer_over_name;
        }


        $vehicle->save();
        VehicleAliasModel::where('fk_vehicle',"=",$vehicle->id)->delete();
        for($i=1; $i <= $request->num_alias; $i++) { 
            if($request->has("alias_" . $i)){
                VehicleAliasModel::insert([
                    "alias" => $request->input("alias_" . $i),
                    "fk_vehicle" => $vehicle->id
                ]);                
            } 
        }
        return redirect()->route('vehicles.index',["id" => $vehicle->fk_contractor])->with('message', 'Vehicle modified successfully!');
    }
    
    public function by_contractor($idContractor){

        $vehicles = VehiclesModel::where("fk_contractor","=",$idContractor)->get();
        
        return response()->json([
            "success" => true,
            "vehicles" => $vehicles
        ]);
    }

}
