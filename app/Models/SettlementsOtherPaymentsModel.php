<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettlementsOtherPaymentsModel extends Model
{
    use HasFactory;
    protected $table = "settlements_other_payments";

    protected $fillable = [
        "fk_other_payments",
        "fk_settlement"
    ];
    

    public $timestamps = false;

    public function settlement(){
        return $this->belongsTo(SettlementsModel::class, 'fk_settlement', 'id');
    }

    public function other_payments(){
        return $this->belongsTo(OtherPaymentsModel::class, 'fk_other_payments', 'id');
    }
}
