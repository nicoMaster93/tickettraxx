<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeductionVehiclesModel extends Model
{
    use HasFactory;
    protected $table = "deduction_vehicles";

    protected $fillable = [
        "date",
        "fk_deduction",
        "fk_vehicle",
        "city",
        "state",
        "gallons",
        "total"
    ];

    public $timestamps = false;

    public function deduction(){
        return $this->belongsTo(DeductionsModel::class, 'fk_deduction', 'id');
    }

    public function vehicle(){
        return $this->belongsTo(VehiclesModel::class, 'fk_vehicle', 'id');
    }

}
