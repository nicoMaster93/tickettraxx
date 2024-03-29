<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigModel extends Model
{
    use HasFactory;
    protected $table = "config";

    protected $fillable = [
        "fee",
        "insurance"
    ];
    public $timestamps = false;
}
