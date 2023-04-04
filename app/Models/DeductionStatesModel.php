<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeductionStatesModel extends Model
{
    use HasFactory;
    protected $table = "deduction_states";

    protected $fillable = [
        "name"
    ];

    public $timestamps = false;
}
