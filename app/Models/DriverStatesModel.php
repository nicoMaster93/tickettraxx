<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverStatesModel extends Model
{
    use HasFactory;
    protected $table = "driver_states";

    protected $fillable = [
        "name"
    ];

    public $timestamps = false;
}
