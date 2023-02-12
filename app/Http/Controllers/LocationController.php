<?php

namespace App\Http\Controllers;

use App\Models\LocationModel;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function cities_by_state($idState = "0"){
        
        $cities = LocationModel::where('fk_location',"=",$idState)->get();

        return response()->json([
            "success" => true,
            "cities" => $cities
        ]);
    }
}
