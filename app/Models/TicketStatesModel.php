<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketStatesModel extends Model
{
    use HasFactory;

    protected $table = "ticket_states";

    protected $fillable = [
        "name"
    ];

    public $timestamps = false;
}
