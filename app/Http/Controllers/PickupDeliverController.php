<?php

namespace App\Http\Controllers;

use App\Http\Requests\PickupDeliverRequest;
use App\Models\PickupDeliverModel;
use App\Models\PO_CodesModel;
use Illuminate\Http\Request;

class PickupDeliverController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        
        $pickups_delivers = PickupDeliverModel::all();
        
        return view('pickup_deliver/lista', [
            "pickups_delivers" => $pickups_delivers
        ]);        
    }

    public function createForm(){        
        return view('pickup_deliver/create', [
        ]);
    }

    public function create(PickupDeliverRequest $request){
        $pickup_deliver = new PickupDeliverModel;
        $pickup_deliver->type = $request->type;
        $pickup_deliver->place = $request->place;
        $pickup_deliver->save();
        return redirect()->route('pickup_deliver.index')->with('message', 'Pickup/Deliver created successfully!');
    }

    public function editForm($id){
        
        $pickup_deliver = PickupDeliverModel::findOrFail($id);
        
        return view('pickup_deliver/edit', [
            "pickup_deliver" => $pickup_deliver
        ]);
    }

    public function update($id, PickupDeliverRequest $request){
        $pickup_deliver = PickupDeliverModel::findOrFail($id);
        $pickup_deliver->type = $request->type;
        $pickup_deliver->place = $request->place;
        $pickup_deliver->save();
        return redirect()->route('pickup_deliver.index')->with('message', 'Pickup/Deliver updated successfully!');
    }

    public function delete($id){
        $po_codes = PO_CodesModel::where("fk_pickup", "=", $id)->orWhere("fk_deliver", "=", $id)->count();
        if($po_codes == 0){
            PickupDeliverModel::where("id","=",$id)->delete();            
        return redirect()->route('pickup_deliver.index')->with('message', 'Pickup/Deliver deleted successfully!');
        }
        else{
            return redirect()->route('pickup_deliver.index')->withErrors(['pickup_deliver' => 'The Pickup/Deliver could not be deleted, it has related PO codes']);
        }

        
        
    }

}

