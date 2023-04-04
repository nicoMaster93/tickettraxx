<?php

namespace App\Http\Controllers;

use App\Http\Requests\Driver\CreateRequest;
use App\Models\ContractorsModel;
use App\Models\DriversModel;
use App\Models\LocationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class DriversContractorController extends Controller
{
 
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){

        $contractor = Auth::user()->contractor;
        $drivers = DriversModel::where("fk_contractor", $contractor->id)->where("fk_driver_state", "1")->get();

        return view('drivers_contractor/lista', [
            "drivers" => $drivers,
            "contractor" => $contractor
        ]);
    }

    public function createForm(){
        
        $contractor = Auth::user()->contractor;
        $location_states = LocationModel::whereNull("fk_location")->get();
        

        $location_cities = [];
        $errors = Session::get('errors');
        if(isset($errors)){
            $olds = Session::getOldInput();
            $location_cities = LocationModel::where("fk_location","=",$olds['state'])->get();
        }

        return view('drivers_contractor/create', [
            "location_states" => $location_states,
            "location_cities" => $location_cities,
            "contractor" => $contractor
        ]);
    }

    public function create(CreateRequest $request){        
        if((!$request->hasFile("photo_cdl") && !$request->has("photo_cdl_box_data")) ||
            (!$request->hasFile("photo_cdl") && empty($request->photo_cdl_box_data))){
            return Funciones::sendFailedResponse(["photo_cdl_res" => "Picture of CDL is required"]);
        }
        if((!$request->hasFile("photo_medical_card") && !$request->has("photo_medical_card_box_data")) ||
            (!$request->hasFile("photo_medical_card") && empty($request->photo_medical_card_box_data))){
            return Funciones::sendFailedResponse(["photo_medical_card_res" => "Picture of Medical Card is required"]);
        }        

        if($request->state == "other"){
            $state = new LocationModel();
            $state->location_name = $request->other_state;
            $state->location_type = "State";
            $state->save();
        }
        else{
            $state = LocationModel::findOrFail($request->state);
        }

        if($request->city == "other"){
            $city = new LocationModel();
            $city->location_name = $request->other_city;
            $city->location_type = "City";
            $city->fk_location = $state->id;
            $city->save();
        }
        else{
            $city = LocationModel::findOrFail($request->city);
        }

        $folder = "public/driver_files/";
        if($request->hasFile("photo_cdl")){
            //Agregar archivo por file            
            $file_cdl_name =  uniqid()."_".$request->file("photo_cdl")->getClientOriginalName();
            $request->file("photo_cdl")->storeAs($folder, $file_cdl_name, "local");
        }
        else{
            //Agregar file por texto
            $file_cdl_name =  uniqid()."_".$request->photo_cdl_box_name;
            Funciones::subirBase64($request->photo_cdl_box_data, $folder.$file_cdl_name);           
        }

        if($request->hasFile("photo_medical_card")){
            //Agregar archivo por file            
            $file_medical_card_name =  uniqid()."_".$request->file("photo_medical_card")->getClientOriginalName();
            $request->file("photo_medical_card")->storeAs($folder, $file_medical_card_name, "local");
        }
        else{
            //Agregar file por texto
            $file_medical_card_name = uniqid()."_".$request->photo_medical_card_box_name;
            Funciones::subirBase64($request->photo_medical_card_box_data, $folder.$file_medical_card_name);           
        }

        
        $contractor = Auth::user()->contractor;

        $driver = new DriversModel();
        $driver->name = $request->name;
        $driver->phone = $request->phone;
        $driver->email = $request->email;
        $driver->address = $request->address;
        $driver->fk_contractor = $contractor->id;
        $driver->fk_driver_state = 1;
        $driver->fk_location_city = $city->id;
        $driver->photo_cdl = $folder.$file_cdl_name;
        $driver->photo_medical_card = $folder.$file_medical_card_name;
        $driver->save();

        return redirect()->route('drivers_contractor.index')->with('message', 'Driver created successfully!');
    }


    public function delete($id){
        
        $driver = DriversModel::findOrFail($id);
        $driver->fk_driver_state = 2;
        $driver->save();
        
        return redirect()->route('drivers_contractor.index')->with('message', 'Driver deleted successfully!');
    }

    public function editForm($id){
        
        $driver = DriversModel::findOrFail($id);
        $contractor = ContractorsModel::findOrFail($driver->fk_contractor);
        $location_states = LocationModel::whereNull("fk_location")->get();
        $location_cities = LocationModel::where("fk_location","=",$driver->location_city->fk_location)->get();
        
        return view('drivers_contractor/edit', [
            "driver" => $driver,
            "location_states" => $location_states,
            "contractor" => $contractor,
            "location_cities" => $location_cities
        ]);
    }

    public function update($id, CreateRequest $request){
        
        if($request->state == "other"){
            $state = new LocationModel();
            $state->location_name = $request->other_state;
            $state->location_type = "State";
            $state->save();
        }
        else{
            $state = LocationModel::findOrFail($request->state);
        }

        if($request->city == "other"){
            $city = new LocationModel();
            $city->location_name = $request->other_city;
            $city->location_type = "City";
            $city->fk_location = $state->id;
            $city->save();
        }
        else{
            $city = LocationModel::findOrFail($request->city);
        }

        

       


        $folder = "public/driver_files/";
        $driver = DriversModel::findOrFail($id);
        if ($request->hasFile('photo_cdl')) {
            if(isset($driver->photo_cdl) && !empty($driver->photo_cdl)){
                Storage::delete($driver->photo_cdl);
            }
            $file_cdl_name =  uniqid()."_".$request->file("photo_cdl")->getClientOriginalName();
            $request->file("photo_cdl")->storeAs($folder, $file_cdl_name, "local");
            $driver->photo_cdl = $folder.$file_cdl_name;
        }
        else if($request->has("photo_cdl_box_data") && !empty($request->input("photo_cdl_box_data"))){
            if(isset($driver->photo_cdl) && !empty($driver->photo_cdl)){
                Storage::delete($driver->photo_cdl);
            }
            $file_cdl_name =  uniqid()."_".$request->photo_cdl_box_name;
            Funciones::subirBase64($request->photo_cdl_box_data, $folder.$file_cdl_name);           
            $driver->photo_cdl = $folder.$file_cdl_name;
        }


        if ($request->hasFile('photo_medical_card')) {
            if(isset($driver->photo_medical_card) && !empty($driver->photo_medical_card)){
                Storage::delete($driver->photo_medical_card);
            }
            $file_medical_card_name =  uniqid()."_".$request->file("photo_medical_card")->getClientOriginalName();
            $request->file("photo_medical_card")->storeAs($folder, $file_medical_card_name, "local");
            $driver->photo_medical_card = $folder.$file_medical_card_name;
        }
        else if($request->has("photo_medical_card_box_data") && !empty($request->input("photo_medical_card_box_data"))){

            if(isset($driver->photo_medical_card) && !empty($driver->photo_medical_card)){
                Storage::delete($driver->photo_medical_card);
            }
            
            $file_medical_card_name =  uniqid()."_".$request->photo_medical_card_box_name;
            Funciones::subirBase64($request->photo_medical_card_box_data, $folder.$file_medical_card_name);           
            $driver->photo_medical_card = $folder.$file_medical_card_name;
        }

        $driver->name = $request->name;
        $driver->phone = $request->phone;
        $driver->email = $request->email;
        $driver->address = $request->address;
      
        $driver->fk_driver_state = 1;
        $driver->fk_location_city = $city->id;
        $driver->save();

        return redirect()->route('drivers_contractor.index')->with('message', 'Driver modified successfully!');
    }
}
