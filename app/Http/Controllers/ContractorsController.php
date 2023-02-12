<?php

namespace App\Http\Controllers;

use App\Http\Requests\Contractor\CreateRequest;
use App\Http\Requests\Contractor\EditQuickBooksRequest;
use App\Http\Requests\Contractor\EditRequest;
use App\Models\ContractorsModel;
use App\Models\LocationModel;
use App\Models\TypeIdsModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class ContractorsController extends Controller
{
    

    public function __construct()
    {
        $this->middleware('auth');
        

    }
    
    public function index(){
        $contractors = ContractorsModel::select("contractors.*", DB::raw("(select v.unit_number from vehicles as v where v.fk_contractor = contractors.id and v.fk_vehicle_state = 1 limit 1) as unit_number"))
        ->whereIn("fk_contractor_state",["1","2"])
        ->get();
        
        return view('contractors/lista', [
            "contractors" => $contractors
        ]);
    }

    public function createForm(){
        $typesIds = TypeIdsModel::all();
        $location_states = LocationModel::whereNull("fk_location")->get();
        
        $location_cities = [];
        $errors = Session::get('errors');
        if(isset($errors)){
            $olds = Session::getOldInput();
            $location_cities = LocationModel::where("fk_location","=",$olds['state'])->get();
        }
        
        return view('contractors/create', [
            "typesIds" => $typesIds,
            "location_states" => $location_states,
            "location_cities" => $location_cities
        ]);
    }

    public function create(CreateRequest $request){

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

        $usuario = new User();
        $usuario->password = Hash::make($request->id);
        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->fk_rol = 2; //Contractor
        $usuario->save();

        $contractor = new ContractorsModel();
        $contractor->name_contact = $request->name;
        $contractor->address = $request->address;
        $contractor->zip_code = $request->zip_code;
        $contractor->id_number = $request->id;
        $contractor->company_name = $request->company_name;
        $contractor->company_telephone = $request->company_telephone;
        $contractor->email = $request->email;
        $contractor->percentage = $request->percentage;
        $contractor->fk_type_ids = $request->id_type;
        $contractor->fk_location_city = $city->id;
        $contractor->fk_user = $usuario->id;
        $contractor->fk_contractor_state = 1;
        $contractor->save();
        return redirect()->route('contractors.index')->with('message', 'Contractor created successfully!');
    }

    public function delete($id){
        $contractor = ContractorsModel::findOrFail($id);
        $contractor->fk_contractor_state = 2;
        $contractor->save();
        return redirect()->route('contractors.index')->with('message', 'Contractor disabled successfully!');
    }

    public function activate($id){
        $contractor = ContractorsModel::findOrFail($id);
        $contractor->fk_contractor_state = 1;
        $contractor->save();
        return redirect()->route('contractors.index')->with('message', 'Contractor activated successfully!');
    }

    public function editForm($id){
        
        $contractor = ContractorsModel::findOrFail($id);
        $typesIds = TypeIdsModel::all();
        $location_states = LocationModel::whereNull("fk_location")->get();
        $location_cities = LocationModel::where("fk_location","=",$contractor->location_city->fk_location)->get();
        
        return view('contractors/edit', [
            "typesIds" => $typesIds,
            "location_states" => $location_states,
            "contractor" => $contractor,
            "location_cities" => $location_cities
        ]);
    }
    
    public function update($id, EditRequest $request){
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

        $contractor = ContractorsModel::findOrFail($id);

        $usuario = User::findOrFail($contractor->fk_user);
        $usuario->password = Hash::make($request->id);
        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->fk_rol = 2; //Contractor
        $usuario->save();

        
        $contractor->name_contact = $request->name;
        $contractor->address = $request->address;
        $contractor->zip_code = $request->zip_code;
        $contractor->id_number = $request->id;
        $contractor->company_name = $request->company_name;
        $contractor->company_telephone = $request->company_telephone;
        $contractor->email = $request->email;
        $contractor->percentage = $request->percentage;
        $contractor->fk_type_ids = $request->id_type;
        $contractor->fk_location_city = $city->id;
        $contractor->fk_user = $usuario->id;
        $contractor->save();
        return redirect()->route('contractors.index')->with('message', 'Contractor modified successfully!');
    }


    

}
