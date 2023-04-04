<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeductionsModel;
use App\Models\DeductionTypesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeductionsContractorController extends Controller
{
    /**
     * Deductions by contractor
     * Shows all deductions by contractor
     * 
	 * @group  v 1.0.0
     * 
     * @bodyParam start_date String optional Start date for ticket filter.
     * @bodyParam end_date String optional End date for ticket filter.
     * 
     * @authenticated
     * 
	 */
    public function show(Request $request){
        //Get contractor by user
        $contractor = auth()->user()->contractor;
        //Get tickets with filter
        $deductions = DeductionsModel::select([
            "deductions.id",
            "total_value",
            "date_pay",
            DB::raw("deduction_types.name as deduction_type"),
            DB::raw("deduction_states.name as deduction_state"),
        ])
        ->join("deduction_types", "deduction_types.id", "=", "fk_deduction_type")
        ->join("deduction_states", "deduction_states.id", "=", "fk_deduction_state")
        ->where("fk_contractor", "=", $contractor->id);
        if($request->has("start_date") && !empty($request->input("start_date"))){
            $deductions = $deductions->where("deductions.date_loan",">=",$request->input("start_date"));
        }
        if($request->has("end_date") && !empty($request->input("end_date"))){
            $deductions = $deductions->where("deductions.date_loan","<=",$request->input("end_date"));
        }
        if($request->has("deductionType") && !empty($request->input("deductionType"))){
            $deductions = $deductions->where("deductions.fk_deduction_type","=",$request->input("deductionType"));
        }

        
        $deductions = $deductions->orderBy("deductions.date_loan","desc");

        //Page and number of records by consult
        $page = 0;
        $records = 6;
        if($request->has("page")){
            $page = $request->input("page");
        }        
        $deductions = $deductions->skip($page*$records)->take($records)->get();

        foreach($deductions as $key => $deduction){
            $deduction->total_value = "$ ".number_format($deduction->total_value, 2);
            $deductions[$key] = $deduction;
        }

        return response()->json([
            "success" => true,
            "message" => "Deductions showed successfully",
            "data" => [
                "deductions" => $deductions,
                "current_page" => $page
            ]                    
        ], 200);
    }

    /**
     * Deduction types
     * Shows all deduction types
     * 
	 * @group  v 1.0.0
     * 
     * 
     * @authenticated
     * 
	 */
    public function deduction_types(Request $request){
        $deduction_types = DeductionTypesModel::all();
        return response()->json([
            "success" => true,
            "message" => "Deduction types showed successfully",
            "data" => [
                "deduction_types" => $deduction_types
            ]                    
        ], 200);
    }
}
