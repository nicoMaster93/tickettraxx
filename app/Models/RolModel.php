<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolModel extends Model
{
    use HasFactory;
    
    protected $table = "rol";

    protected $fillable = [
        "name"
    ];

    public $timestamps = false;
}