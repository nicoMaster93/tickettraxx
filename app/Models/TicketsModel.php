<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketsModel extends Model
{
    use HasFactory;

    protected $table = "tickets";

    protected $fillable = [
        "number",
        "date_gen",
        "date_pay",
        "pickup",
        "deliver",
        "file",
        "tonage",
        "rate",
        "total",
        "surcharge",
        "return_message",
        "fk_vehicle",
        "fk_material",
        "fk_ticket_state",
        "fk_surcharge",
        "fk_customer"
    ];

    public $timestamps = false;
    
    public function ticket_state(){
        return $this->belongsTo(TicketStatesModel::class, 'fk_ticket_state', 'id');
    }

    public function material(){
        return $this->belongsTo(MaterialsModel::class, 'fk_material', 'id');
    }

    public function vehicle(){
        return $this->belongsTo(VehiclesModel::class, 'fk_vehicle', 'id');
    }

    public function settlement_ticket(){
        return $this->hasMany(SettlementsTicketsModel::class, 'fk_ticket', 'id');
    }

    public function customer(){
        return $this->belongsTo(CustomerModel::class, 'fk_customer', 'id');
    }
}
