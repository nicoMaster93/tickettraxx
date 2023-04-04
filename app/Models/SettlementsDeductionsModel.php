<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettlementsDeductionsModel extends Model
{
    use HasFactory;

    protected $table = "settlements_deduction";

    protected $fillable = [
        "value",
        "fk_deduction",
        "fk_settlement",
        "fk_vehicle"
    ];
    

    public $timestamps = false;

    public function settlement(){
        return $this->belongsTo(SettlementsModel::class, 'fk_settlement', 'id');
    }
    
    public function deduction(){
        return $this->belongsTo(DeductionsModel::class, 'fk_deduction', 'id');
    }

    public function vehicle(){
        return $this->belongsTo(VehiclesModel::class, 'fk_vehicle', 'id');
    }
}
