<?php

namespace App\Http\Controllers;

use App\Http\Requests\Deduction\CreateRequest;
use App\Models\ConfigModel;
use App\Models\ContractorsModel;
use App\Models\DeductionsModel;
use App\Models\DeductionTypesModel;
use App\Models\DeductionVehiclesModel;
use App\Models\SettlementsDeductionsModel;
use App\Models\SettlementsModel;
use App\Models\VehiclesModel;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use PhpParser\Node\Expr\Empty_;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class DeductionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private $arrCambios = [
        "$" => "",
        "," => ".",
        "USD / 1 TON" => ""
    ];

    public function index(Request $request){

        $deductions = DeductionsModel::whereIn("fk_deduction_state",["1","2"]);

        if($request->has("start_date") && !empty($request->input("start_date"))){
            $deductions->where("date_loan", ">=", $request->start_date);
        }
        if($request->has("end_date") && !empty($request->input("end_date"))){
            $deductions->where("date_loan", "<=", $request->end_date);
        }
        if($request->has("deduction_type") && !empty($request->input("deduction_type"))){
            $deductions->where("fk_deduction_type", "=", $request->deduction_type);
        }

        $deductions = $deductions->get();

        $deduction_types = DeductionTypesModel::all();
        
        return view('deductions/lista', [
            "deductions" => $deductions,
            "deduction_types" => $deduction_types,
            "start_date" => $request->start_date,
            "end_date" => $request->end_date,
            "deduction_type" => $request->deduction_type,
        ]);
    }

    public function createForm(){

        $deduction_types = DeductionTypesModel::all();
        $contractors = ContractorsModel::orderBy("company_name")->get();
        $vehicles = array();
        $errors = Session::get('errors');
        if(isset($errors)){
            $olds = Session::getOldInput();
            $vehicles = VehiclesModel::where("fk_contractor","=",$olds['contractor'])->get();
        }
        
        return view('deductions/create', [
            "deduction_types" => $deduction_types,
            "contractors" => $contractors,
            "vehicles" => $vehicles
        ]);
    }

    public function create(CreateRequest $request){
        if($request->input("deduction_type") == "1"){
            $deduccion = new DeductionsModel;
            $deduccion->fk_deduction_type = $request->deduction_type;
            $deduccion->total_value = $request->total_value;
            $deduccion->balance_due = $request->balance_due;
            $deduccion->date_loan = $request->date_loan;
            
            $date_pay = new DateTime($request->date_loan);
            $date_pay->add(new DateInterval("P".$request->days."D"));

            $deduccion->date_pay = $date_pay->format('Y-m-d');

            if($request->charge_type == "number_installments"){
                $deduccion->number_installments = $request->number_installments;
            }
            else if($request->charge_type == "fixed_value"){
                $deduccion->fixed_value = $request->fixed_value;
            }
            $deduccion->days = $request->days;
            $deduccion->fk_deduction_state = 1;
            $deduccion->fk_contractor = $request->contractor;
            $deduccion->save();
            $this->buscar_settlement($deduccion);
        }
        else if($request->input("deduction_type") == "2"){
            $arrData = array();
            for ($i=1; $i <= $request->input("vehicles"); $i++) {
                
                $arrInt = array();
                $vehicles = VehiclesModel::where("unit_number","=",$request->input('vehicle_'.$i))->first();
                if(!isset($vehicles)){
                    $vehicles = VehiclesModel::select("vehicles.*")
                    ->join("vehicles_alias as va", "va.fk_vehicle", "vehicles.id")
                    ->where("va.alias","=",$request->input('vehicle_'.$i))->first();
                    if(!isset($vehicles)){
                        return Funciones::sendFailedResponse(['vehicle_'.$i => "Vehicle not found"]);
                    }
                    else{
                        $arrInt["vehicle"] = $vehicles->id;
                        $arrInt["contractor"] = $vehicles->fk_contractor;
                    }
                }
                else{
                    $arrInt["vehicle"] = $vehicles->id;
                    $arrInt["contractor"] = $vehicles->fk_contractor;
                }

                $date_pay = new DateTime($request->input('date_vehicle_'.$i));
                $date_pay->add(new DateInterval("P14D"));

                $inicio = new DateTime($date_pay->format('Y-m-d'));
                $inicio->modify('monday this week');


                $arrInt["date_pay"] = $date_pay->format("Y-m-d");
                $arrInt["date_loan"] = $request->input('date_vehicle_'.$i);

                $arrInt["city"] = $request->input('city_'.$i);
                $arrInt["state"] = $request->input('state_'.$i);
                $arrInt["gallons"] = $request->input('gallons_'.$i);
                $arrInt["total"] = $request->input('total_'.$i);


                $arrData[$inicio->format("Y-m-d")][$arrInt["contractor"]] = $arrData[$inicio->format("Y-m-d")][$arrInt["contractor"]] ?? array();
                


                array_push($arrData[$inicio->format("Y-m-d")][$arrInt["contractor"]],$arrInt);
            }
            
            foreach($arrData as $date_pay => $data1){
                foreach($data1 as $contractor => $data){
                    
                    $deduccion = new DeductionsModel;
                    $deduccion->fk_deduction_type = $request->deduction_type;          
                    $config = ConfigModel::findOrFail(1);
                    $total = $config->fee * sizeof($data);                    
                    foreach($data as $veh){
                        $total += $veh["total"];
                    }
                    $deduccion->total_value = $total;
                    $deduccion->balance_due = $total;
                    $deduccion->date_pay = $date_pay;
                    $deduccion->number_installments = 1;
                    $deduccion->fk_deduction_state = 1;
                    $deduccion->fk_contractor = $contractor;
                    $deduccion->save();
                    $this->buscar_settlement($deduccion);
                    
                    foreach($data as $veh){
                        $deduction_vehicle = new DeductionVehiclesModel;
                        $deduction_vehicle->date = $veh["date_loan"];
                        $deduction_vehicle->fk_deduction = $deduccion->id;
                        $deduction_vehicle->fk_vehicle = $veh["vehicle"];
                        $deduction_vehicle->city = $veh["city"];
                        $deduction_vehicle->state = $veh["state"];
                        $deduction_vehicle->gallons = $veh["gallons"];
                        $deduction_vehicle->total = $veh["total"];
                        $deduction_vehicle->save();
                    }
                    
                    
                }
                
            }
            

        }
        else if($request->input("deduction_type") == "3"){
            $deduccion = new DeductionsModel;
            $deduccion->fk_deduction_type = $request->deduction_type;
            $deduccion->date_loan = $request->date_loan;
            $deduccion->date_pay = $request->date_loan;
            $deduccion->days = 7;
            $deduccion->fk_deduction_state = 1;
            $deduccion->fk_contractor = $request->contractor;
            $deduccion->save();

            $this->buscar_settlement($deduccion);
        }
        else if($request->input("deduction_type") == "4"){
            $deduccion = new DeductionsModel;
            $deduccion->fk_deduction_type = $request->deduction_type;
            $deduccion->date_loan = $request->date_loan;
            $deduccion->date_pay = $request->date_loan;
            $deduccion->days = 30;
            $deduccion->fk_deduction_state = 1;
            $deduccion->fk_contractor = $request->contractor;
            $deduccion->save();

            $this->buscar_settlement($deduccion);
        }
        
        return redirect()->route('deductions.index')->with('message', 'Deduction created successfully!');
    }

    private function buscar_settlement($deduction){
        //Cargar el primero que pertenezca a ese contratista, en esa fecha de pago y que este en estado sin pagar
        $settlement = SettlementsModel::where("start_date", "<=", $deduction->date_pay)
        ->where("end_date", ">=", $deduction->date_pay)
        ->where("fk_contractor", "=", $deduction->fk_contractor)
        ->where("fk_settlement_state", "=", "1")
        ->first();

        if(isset($settlement)){
            
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

                $date_pay = new DateTime($deduction->date_pay);
                $date_pay->add(new DateInterval("P".$deduction->days."D"));
                $deduction->date_pay = $date_pay->format('Y-m-d');
                $deduction->save();
                $this->buscar_settlement($deduction);
                
            }
            else if($deduction->fk_deduction_type == "4"){
                $vehicles = VehiclesModel::select(DB::raw("count(*) as cuenta"))
                ->where("fk_contractor","=", $deduction->fk_contractor)
                ->where("fk_vehicle_state","=","1")
                ->first();
                $config = ConfigModel::findOrFail(1);
                $deductionValue = $config->insurance * ($vehicles->cuenta ?? 0);
                $settlement->deduction = $settlement->deduction + $deductionValue;
                $settlement->for_contractor = $settlement->for_contractor - $deductionValue;

                $date_pay = new DateTime($deduction->date_pay);
                $date_pay->add(new DateInterval("P1M"));
                $deduction->date_pay = $date_pay->format('Y-m-d');
                $deduction->save();
                $this->buscar_settlement($deduction);
                
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
                $date_pay = new DateTime($deduction->date_pay);
                $date_pay->add(new DateInterval("P".$deduction->days."D"));                
                $deduction->date_pay = $date_pay->format('Y-m-d');
                $deduction->save();
                $this->buscar_settlement($deduction);
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
        else{
            
            $settlement = SettlementsModel::where("start_date", ">", $deduction->date_pay)            
            ->where("fk_contractor", "=", $deduction->fk_contractor)
            ->where("fk_settlement_state", "=", "1")
            ->orderBy("start_date","asc")
            ->first();
  

            if(isset($settlement)){

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

                    $date_pay = new DateTime($settlement->end_date);
                    $date_pay->add(new DateInterval("P".$deduction->days."D"));
                    $deduction->date_pay = $date_pay->format('Y-m-d');
                    $deduction->save();
                    $this->buscar_settlement($deduction);
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
                    $date_pay = new DateTime($settlement->end_date);
                    $date_pay->add(new DateInterval("P".$deduction->days."D"));
                    $deduction->date_pay = $date_pay->format('Y-m-d');
                    $deduction->save();
                    $this->buscar_settlement($deduction);
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
        }
    }
    
    public function delete($id){
        $deduction = DeductionsModel::findOrFail($id);
        $relations = SettlementsDeductionsModel::where("fk_deduction", "=", $id)->get();
        foreach($relations as $relation){
            $relation->settlement->deduction = $relation->settlement->deduction - $deduction->total_value;
            $relation->settlement->for_contractor = $relation->settlement->for_contractor + $deduction->total_value;
            $relation->settlement->save();
        }
        $deduction->delete();
            
        return redirect()->route('deductions.index')->with('message', 'Deduction deleted successfully!');
        
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
        $folder = "public/deductions_files/";
        $file_name =  time()."_.csv";
        Funciones::subirBase64($request->file64, $folder.$file_name);      
        $contents = Storage::get($folder.$file_name);
            
        $reader = Reader::createFromString($contents);
        $reader->setDelimiter(';');
        $arrData = array();
        foreach ($reader as $index => $row) {            
            try{
                if($index == 0 && strtolower($row[0]) === 'date loan'){
                    /* Es el encabezado  */
                    continue;
                }
                foreach($row as $key =>$valor){
                    if($valor==""){
                        $row[$key]=null;
                    }
                    else{
                        $row[$key] = utf8_encode($row[$key]);
                    }  
                }
                

                $arrInt = array();
                $vehicles = VehiclesModel::where("unit_number","=",$row[1])->first();
                if(!isset($vehicles)){
                    $vehicles = VehiclesModel::select("vehicles.*")
                    ->join("vehicles_alias as va", "va.fk_vehicle", "vehicles.id")
                    ->where("va.alias","=",$row[1])->first();
                    if(!isset($vehicles)){
                        array_push($errors, "Vehicle not found on row ".($index+1));
                        continue;
                    }
                    else{
                        $arrInt["vehicle"] = $vehicles->id;
                        $arrInt["contractor"] = $vehicles->fk_contractor;
                    }
                }
                else{
                    $arrInt["vehicle"] = $vehicles->id;
                    $arrInt["contractor"] = $vehicles->fk_contractor;
                }

                
                $datePrev = explode("/",$row[0]);

                /* año - mes - dia */
                $date_gen = $datePrev[2]."-".$datePrev[1]."-".$datePrev[0];
                
                $date_pay = new DateTime($date_gen);
                $date_pay->add(new DateInterval("P14D"));
                
                
                $inicio = new DateTime($date_pay->format('Y-m-d'));
                $inicio->modify('monday this week');
                $arrInt["date_pay"] = $date_pay->format("Y-m-d");
                $arrInt["date_loan"] = $date_gen;

                $row[5] = $this->corregir_data($row[5]);

                $arrInt["city"] = $row[2];
                $arrInt["state"] = $row[3];
                $arrInt["gallons"] = $row[4];
                $arrInt["total"] = $row[5];
                /* Agrego el type  */
                $arrInt["type"] = $row[6];

                $arrData[$inicio->format("Y-m-d")][$arrInt["contractor"]] = $arrData[$inicio->format("Y-m-d")][$arrInt["contractor"]] ?? array();
                array_push($arrData[$inicio->format("Y-m-d")][$arrInt["contractor"]],$arrInt);
            }catch(Exception $e){
                array_push($errors, "Error ".$e->getMessage()." on row ".($index+1));
            }          
        }
        
        foreach($arrData as $date_pay => $data1){
            foreach($data1 as $contractor => $data){
                $deduccion = new DeductionsModel;
                // $deduccion->fk_deduction_type = "2"; 
                /* Se adiciona el type desde el cargue JN */         
                $config = ConfigModel::findOrFail(1);
                
                $total = $config->fee * sizeof($data);
                foreach($data as $veh){
                    $total += $veh["total"];
                    $deduccion->fk_deduction_type = $veh['type']; 
                }
                $deduccion->total_value = $total;
                $deduccion->balance_due = $total;
                $deduccion->date_pay = $date_pay;
                $deduccion->number_installments = 1;
                $deduccion->fk_deduction_state = 1;
                $deduccion->fk_contractor = $contractor;
                $deduccion->save();
                $this->buscar_settlement($deduccion);
                
                foreach($data as $veh){
                    $deduction_vehicle = new DeductionVehiclesModel;
                    $deduction_vehicle->date = $veh["date_loan"];
                    $deduction_vehicle->fk_deduction = $deduccion->id;
                    $deduction_vehicle->fk_vehicle = $veh["vehicle"];
                    $deduction_vehicle->city = $veh["city"];
                    $deduction_vehicle->state = $veh["state"];
                    $deduction_vehicle->gallons = $veh["gallons"];
                    $deduction_vehicle->total = $veh["total"];

                    $deduction_vehicle->save();
                }
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

    public function details($id){
        $deductions = DeductionVehiclesModel::where("fk_deduction","=",$id)->get();

        $html = view('deductions/ajax/details', ["deductions" => $deductions])->render();
        return response()->json([
            "success" => true,
            "html" => $html
        ]);
    }

    public function updateForm(){

        $deduction_types = DeductionTypesModel::all();
        $contractors = ContractorsModel::orderBy("company_name")->get();
        $vehicles = array();
        $errors = Session::get('errors');
        if(isset($errors)){
            $olds = Session::getOldInput();
            $vehicles = VehiclesModel::where("fk_contractor","=",$olds['contractor'])->get();
        }
        
        return view('deductions/create', [
            "deduction_types" => $deduction_types,
            "contractors" => $contractors,
            "vehicles" => $vehicles
        ]);
    }

    public function corregir_data($string){
        foreach($this->arrCambios as $cambio => $valor){
            $string = trim(str_replace($cambio, $valor, $string));
        }
        return $string;
    }
    
    public function download_template(){
        /* Elimino la existencia del zip anterior */
        if(file_exists(public_path('app/public/plantillas/deduction_template.zip'))){
            unlink( public_path('app/public/plantillas/deduction_template.zip') );
        }
        // Crea un nuevo archivo ZIP
        $zip = new ZipArchive;
        $base = public_path('storage/plantillas/');
        $filename = $base . 'deduction_template.zip';
        if ($zip->open($filename, ZipArchive::CREATE) !== TRUE) {
            exit('No se pudo crear el archivo ZIP');
        }

        // Agrega los archivos al archivo ZIP
        $archivos = ['deduction_Base.csv','deduction_types.csv'];
        foreach ($archivos as $archivo) {
            $ruta_archivo = $base . $archivo;
            $zip->addFile($ruta_archivo, $archivo);
        }
        // Cierra el archivo ZIP
        $zip->close();
        return response()->download($filename, 'deduction_template.zip');

    }
}
