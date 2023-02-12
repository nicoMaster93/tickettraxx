<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingsRequest;
use App\Models\ConfigModel;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $config = ConfigModel::findOrFail(1);
        return view("settings/settings",[
            "config" => $config
        ]);
    }

    public function update(SettingsRequest $request){
        $config = ConfigModel::findOrFail(1);
        $config->fee = $request->fee;
        $config->insurance = $request->insurance;
        $config->gpsDashCam = $request->gpsDashCam;
        
        $config->save();

        return redirect()->route('settings')->with('message', 'Settings updated successfully!');
    }
    
}
