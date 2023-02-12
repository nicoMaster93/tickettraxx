<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleDriversModel extends Model
{
    use HasFactory;
    protected $table = "vehicles_drivers";

    protected $fillable = [
        "fk_vehicle",
        "fk_driver"
    ];

    public $timestamps = false;

    
}
