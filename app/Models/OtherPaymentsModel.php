<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherPaymentsModel extends Model
{
    use HasFactory;
    protected $table = "other_payments";

    protected $fillable = [
        "date_pay",
        "description",
        "total",
        "fk_contractor",
        "fk_other_payment_state"
    ];
    

    public $timestamps = false;

    public function contractor()
    {
        return $this->belongsTo(ContractorsModel::class, 'fk_contractor', 'id');
    }

    public function state()
    {
        return $this->belongsTo(OtherPaymentStatesModel::class, 'fk_other_payment_state', 'id');
    }


}
