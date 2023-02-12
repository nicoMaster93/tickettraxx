<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleAliasModel extends Model
{
    use HasFactory;
    protected $table = "vehicles_alias";

    protected $fillable = [
        "alias",
        "fk_vehicle"
    ];

    public $timestamps = false;

    
}
