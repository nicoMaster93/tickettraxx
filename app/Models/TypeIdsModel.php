<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeIdsModel extends Model
{
    use HasFactory;
   
    protected $table = "type_ids";

    protected $fillable = [
        "type_name"
    ];

    public $timestamps = false;
}
