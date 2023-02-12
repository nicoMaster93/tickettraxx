<?php

namespace App\Http\Controllers;

use App\Http\Requests\MaterialsRequest;
use App\Models\MaterialsModel;
use App\Models\TicketsModel;
use Illuminate\Http\Request;

class MaterialsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        
        $materials = MaterialsModel::all();
        
        return view('materials/lista', [
            "materials" => $materials
        ]);        
    }

    public function createForm(){        
        return view('materials/create', [
        ]);
    }

    public function create(MaterialsRequest $request){
        $material = new MaterialsModel;
        $material->name = $request->name;
        $material->save();
        return redirect()->route('materials.index')->with('message', 'Material created successfully!');
    }

    public function editForm($id){
        
        $material = MaterialsModel::findOrFail($id);
        
        return view('materials/edit', [
            "material" => $material
        ]);
    }

    public function update($id, MaterialsRequest $request){
        $material = MaterialsModel::findOrFail($id);
        $material->name = $request->name;
        $material->save();
        return redirect()->route('materials.index')->with('message', 'Material updated successfully!');
    }

    public function delete($id){
        
        $tickets = TicketsModel::where("fk_material", "=", $id)->count();
        if($tickets == 0){
            MaterialsModel::where("id","=",$id)->delete();
            
            return redirect()->route('materials.index')->with('message', 'Material deleted successfully!');
        }
        else{
            return redirect()->route('materials.index')->withErrors(['material' => 'The material could not be deleted, it has related tickets']);
        }
        
    }

}
