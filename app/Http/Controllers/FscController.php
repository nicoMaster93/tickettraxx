<?php

namespace App\Http\Controllers;

use App\Http\Requests\FscRequest;
use App\Models\CustomerModel;
use App\Models\FscModel;
use App\Models\TicketsModel;
use Illuminate\Http\Request;

class FscController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $fsc = FscModel::all();
        
        return view('fsc/lista', [
            "fsc" => $fsc
        ]);    
    }

    public function createForm(){        

        $customers = CustomerModel::all();
        return view('fsc/create', [
            "customers" => $customers
        ]);
    }

    public function create(FscRequest $request){
        $fscBusqueda = FscModel::where("fk_customer","=",$request->customer)
        ->where(function ($query) use ($request) {
            $query->whereBetween('from', [$request->from, $request->to])
            ->orWhereBetween('to', [$request->from, $request->to])
            ->orWhere(function ($query2) use ($request) {
                $query2->where("from","<=",$request->from)
                ->where("to",">=",$request->to);
            });
        })->first();
        if(isset($fscBusqueda)){
            return Funciones::sendFailedResponse([
                "from" => "Fsc between other date range configured for this customer",
                "to" => "Fsc between other date range configured for this customer"
            ]);
        }

        $fsc = new FscModel();
        $fsc->from = $request->from;
        $fsc->to = $request->to;
        $fsc->percentaje = $request->percentaje;
        $fsc->fk_customer = $request->customer;
        $fsc->save();
        return redirect()->route('fsc.index')->with('message', 'FSC created successfully!');
    }


    public function editForm($id){
        
        $fsc = FscModel::findOrFail($id);
        $customers = CustomerModel::all();
        return view('fsc/edit', [
            "fsc" => $fsc,
            "customers" => $customers
        ]);
    }

    public function update($id, FscRequest $request){
        $fscBusqueda = FscModel::where("fk_customer","=",$request->customer)
        ->where(function ($query) use ($request) {
            $query->whereBetween('from', [$request->from, $request->to])
            ->orWhereBetween('to', [$request->from, $request->to])
            ->orWhere(function ($query2) use ($request) {
                $query2->where("from","<=",$request->from)
                ->where("to",">=",$request->to);
            });
        })
        ->where("id","<>",$id)
        ->first();
        if(isset($fscBusqueda)){
            return Funciones::sendFailedResponse([
                "from" => "Fsc between other date range configured for this customer",
                "to" => "Fsc between other date range configured for this customer"
            ]);
        }

        $fsc = FscModel::findOrFail($id);
        $fsc->from = $request->from;
        $fsc->to = $request->to;
        $fsc->percentaje = $request->percentaje;
        $fsc->fk_customer = $request->customer;
        $fsc->save();
        return redirect()->route('fsc.index')->with('message', 'FSC updated successfully!');
    }
    
    public function delete($id){
        
        $tickets = TicketsModel::where("fk_surcharge", "=", $id)->count();
        if($tickets == 0){
            FscModel::where("id","=",$id)->delete();
            
            return redirect()->route('fsc.index')->with('message', 'FSC deleted successfully!');
        }
        else{
            return redirect()->route('fsc.index')->withErrors(['material' => 'The FSC could not be deleted, it has related tickets']);
        }
        
    }

}
