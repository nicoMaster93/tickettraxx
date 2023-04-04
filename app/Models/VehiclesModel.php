<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehiclesModel extends Model
{
    use HasFactory;
    protected $table = "vehicles";

    protected $fillable = [
        "unit_number",
        "truck_model_brand",
        "truck_year",
        "truck_vin_number",
        "trailer_model_brand",
        "trailer_year",
        "trailer_vin_number",
        "fk_contractor",
        "fk_vehicle_state",
        "photo_truck_dot_inspection",
        "photo_truck_registration",
        "photo_trailer_dot_inspection",
        "photo_trailer_registration",
        "photo_trailer_over"
    ];
    

    public $timestamps = false;

    public function vehicle_state(){
        return $this->belongsTo(VehicleStatesModel::class, 'fk_vehicle_state', 'id');
    }
    
    public function alias()
    {
        return $this->hasMany(VehicleAliasModel::class, 'fk_vehicle', 'id');
    }

    public function drivers()
    {
        return $this->hasMany(VehicleDriversModel::class, 'fk_vehicle', 'id');
    }

    public function contractor()
    {
        return $this->belongsTo(ContractorsModel::class, 'fk_contractor', 'id');
    }

}
