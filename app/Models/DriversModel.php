<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriversModel extends Model
{
    use HasFactory;
    protected $table = "drivers";

    protected $fillable = [
        "name",
        "phone",
        "email",
        "address",
        "fk_contractor",
        "fk_driver_state",
        "fk_location_city",
        "photo_cdl",
        "photo_medical_card"
    ];

    public $timestamps = false;

    public function contractor()
    {
        return $this->belongsTo(ContractorsModel::class, 'fk_contractor', 'id');
    }
    
    public function driver_state(){
        return $this->belongsTo(DriverStatesModel::class, 'fk_driver_state', 'id');
    }

    public function location_city()
    {
        return $this->belongsTo(LocationModel::class, 'fk_location_city', 'id');
    }

    public function vehicles()
    {
        return $this->hasMany(VehicleDriversModel::class, 'fk_driver', 'id');
    }
}

