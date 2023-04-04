<?php

namespace App\Http\Controllers;

use App\Http\Requests\OtherPaymentsRequest;
use App\Models\ContractorsModel;
use App\Models\OtherPaymentsModel;
use App\Models\SettlementsModel;
use App\Models\SettlementsOtherPaymentsModel;
use Illuminate\Http\Request;

class OtherPaymentsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $other_payments = OtherPaymentsModel::all();

        return view('other_payments/lista', [
            "other_payments" => $other_payments            
        ]);
    }
    
    public function createForm(){
        $contractors = ContractorsModel::orderBy("company_name")->get();
        return view('other_payments/create', [
            "contractors" => $contractors
        ]);
        
    }

    public function create(OtherPaymentsRequest $request){
        $other_payment = new OtherPaymentsModel;
        $other_payment->description = $request->input("description");
        $other_payment->date_pay = $request->input("date_pay");
        $other_payment->total = $request->input("total");
        $other_payment->fk_contractor = $request->input("contractor");
        $other_payment->fk_other_payment_state = 1; //Searching settlement
        $other_payment->save();

        $settlement = SettlementsModel::where("start_date", "<=", $other_payment->date_pay)
        ->where("end_date", ">=", $other_payment->date_pay)
        ->where("fk_contractor", "=", $other_payment->fk_contractor)
        ->where("fk_settlement_state", "=", "1")
        ->first();

        if(isset($settlement)){
            
            $settlement->other_payments = $settlement->other_payments + $other_payment->total;
            $settlement->for_contractor = $settlement->for_contractor + $other_payment->total;
            $settlement->save();

            $setttlement_payment = new SettlementsOtherPaymentsModel;
            $setttlement_payment->fk_other_payments = $other_payment->id;
            $setttlement_payment->fk_settlement = $settlement->id;
            $setttlement_payment->save();


            $other_payment->fk_other_payment_state = 2; //Added to settlement
            $other_payment->save();
        }
        return redirect()->route('other_payments.index')->with('message', 'Other Payment created successfully!');
    }

    public function delete($id){
        $other_payment = OtherPaymentsModel::findOrFail($id);
        $relations = SettlementsOtherPaymentsModel::where("fk_other_payments", "=", $id)->get();
        foreach($relations as $relation){
            $relation->settlement->other_payments = $relation->settlement->other_payments - $other_payment->total;
            $relation->settlement->for_contractor = $relation->settlement->for_contractor - $other_payment->total;
            $relation->settlement->save();
        }
        $other_payment->delete();
            
        return redirect()->route('other_payments.index')->with('message', 'Other Payment deleted successfully!');
        
    }
}
