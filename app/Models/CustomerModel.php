<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerModel extends Model
{
    use HasFactory;
    protected $table = "customer";

    protected $fillable = [
        "full_name",
        "prefix",
        "id_quickbooks"
    ];

    public $timestamps = false;
}
