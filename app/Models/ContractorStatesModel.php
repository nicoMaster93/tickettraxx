<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractorStatesModel extends Model
{
    use HasFactory;
   
    protected $table = "contractor_states";

    protected $fillable = [
        "name"
    ];

    public $timestamps = false;

}
