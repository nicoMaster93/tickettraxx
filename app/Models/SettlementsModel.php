<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettlementsModel extends Model
{
    use HasFactory;
    protected $table = "settlements";

    protected $fillable = [
        "start_date",
        "end_date",
        "subtotal",
        "deduction",
        "other_payments",
        "total",
        "surcharge",
        "for_contractor",
        "fk_contractor",
        "fk_settlement_state",
        "fk_payment"
    ];
    

    public $timestamps = false;

    public function settlement_state(){
        return $this->belongsTo(SettlementStatesModel::class, 'fk_settlement_state', 'id');
    }

    public function contractor(){
        return $this->belongsTo(ContractorsModel::class, 'fk_contractor', 'id');
    }

    public function settlements_tickets()
    {
        return $this->hasMany(SettlementsTicketsModel::class, 'fk_settlement', 'id');
    }

    public function settlements_deductions()
    {
        return $this->hasMany(SettlementsDeductionsModel::class, 'fk_settlement', 'id');
    }

    public function settlements_other_payments()
    {
        return $this->hasMany(SettlementsOtherPaymentsModel::class, 'fk_settlement', 'id');
    }

    public function payment(){
        return $this->belongsTo(PaymentsModel::class, 'fk_payment', 'id');
    }

}
