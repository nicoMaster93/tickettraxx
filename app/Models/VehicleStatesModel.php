<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleStatesModel extends Model
{
    use HasFactory;
    protected $table = "vehicle_states";

    protected $fillable = [
        "name"
    ];

    public $timestamps = false;
}
