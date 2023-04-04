<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeductionsModel extends Model
{
    use HasFactory;
    protected $table = "deductions";

    protected $fillable = [
        "total_value",
        "balance_due",
        "date_loan",
        "date_pay",
        "number_installments",
        "fixed_value",
        "days",
        "fk_deduction_type",
        "fk_deduction_state",
        "fk_contractor"
    ];

    public $timestamps = false;

   
    public function type()
    {
        return $this->belongsTo(DeductionTypesModel::class, 'fk_deduction_type', 'id');
    }

    public function state()
    {
        return $this->belongsTo(DeductionStatesModel::class, 'fk_deduction_state', 'id');
    }

    public function contractor()
    {
        return $this->belongsTo(ContractorsModel::class, 'fk_contractor', 'id');
    }

    public function deduction_vehicles()
    {
        return $this->hasMany(DeductionVehiclesModel::class, 'fk_deduction', 'id');
    }

}