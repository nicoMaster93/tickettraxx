<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupDeliverModel extends Model
{
    use HasFactory;

    protected $table = "pickup_deliver";

    protected $fillable = [
        "type",
        "place"
    ];

    public $timestamps = false;
}
