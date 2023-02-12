<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettlementsTicketsModel extends Model
{
    use HasFactory;
    protected $table = "settlements_tickets";

    protected $fillable = [
        "fk_ticket",
        "fk_settlement"
    ];

    public $timestamps = false;

    public function ticket(){
        return $this->belongsTo(TicketsModel::class, 'fk_ticket', 'id');
    }

    public function settlement(){
        return $this->belongsTo(SettlementsModel::class, 'fk_settlement', 'id');
    }

}
