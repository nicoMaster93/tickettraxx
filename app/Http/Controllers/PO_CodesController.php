<?php

namespace App\Http\Controllers;

use App\Http\Requests\PO_CodesRequest;
use App\Models\PickupDeliverModel;
use App\Models\PO_CodesModel;
use App\Models\TicketsModel;
use Illuminate\Http\Request;

class PO_CodesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        
        $po_codes = PO_CodesModel::all();
        
        return view('po_codes/lista', [
            "po_codes" => $po_codes
        ]);        
    }

    public function createForm(){        

        $pickups = PickupDeliverModel::where("type","=","0")->get();
        $delivers = PickupDeliverModel::where("type","=","1s")->get();

        return view('po_codes/create', [
            "pickups" => $pickups,
            "delivers" => $delivers
        ]);
    }

    public function create(PO_CodesRequest $request){
        $po_code = new PO_CodesModel;
        $po_code->code = $request->code;
        $po_code->fk_pickup = $request->pickup;
        $po_code->fk_deliver = $request->deliver;
        $po_code->rate = $request->rate;
        $po_code->save();
        return redirect()->route('po_codes.index')->with('message', 'PO Code created successfully!');
    }

    public function editForm($id){
        
        $po_code = PO_CodesModel::findOrFail($id);
        $pickups = PickupDeliverModel::where("type","=","0")->get();
        $delivers = PickupDeliverModel::where("type","=","1s")->get();

        return view('po_codes/edit', [
            "po_code" => $po_code,
            "pickups" => $pickups,
            "delivers" => $delivers
        ]);
    }

    public function update($id, PO_CodesRequest $request){
        $po_code = PO_CodesModel::findOrFail($id);
        $po_code->code = $request->code;
        $po_code->fk_pickup = $request->pickup;
        $po_code->fk_deliver = $request->deliver;
        $po_code->rate = $request->rate;
        $po_code->save();
        return redirect()->route('po_codes.index')->with('message', 'PO Code updated successfully!');
    }

    public function delete($id){
        
        $tickets = TicketsModel::where("fk_p_o_code", "=", $id)->count();
        if($tickets == 0){
            PO_CodesModel::where("id","=",$id)->delete();            
            return redirect()->route('po_codes.index')->with('message', 'PO Code deleted successfully!');
        }
        else{
            return redirect()->route('po_codes.index')->withErrors(['po_code' => 'The PO Code could not be deleted, it has related tickets']);
        }
        
    }

    public function getRate($idPickup, $idDeliver){
        $po_code = PO_CodesModel::where("fk_pickup","=",$idPickup)->where("fk_deliver","=",$idDeliver)->first();
        
        return response()->json([
            "success" => isset($po_code),
            "rate" => (isset($po_code) ? $po_code->rate : 0)
        ]);
    }
}

