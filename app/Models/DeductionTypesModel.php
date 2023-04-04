<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeductionTypesModel extends Model
{
    use HasFactory;
    protected $table = "deduction_types";

    protected $fillable = [
        "name"
    ];

    public $timestamps = false;
}
