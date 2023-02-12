<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentsModel extends Model
{
    use HasFactory;
    protected $table = "payments";

    protected $fillable = [
        "date_pay",
        "total"
    ];
    

    public $timestamps = false;

    public function settlements()
    {
        return $this->hasMany(SettlementsModel::class, 'fk_payment', 'id');
    }

}
