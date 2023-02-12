<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherPaymentStatesModel extends Model
{
    use HasFactory;

    protected $table = "other_payment_states";

    protected $fillable = [
        "name"
    ];
    

    public $timestamps = false;


}
