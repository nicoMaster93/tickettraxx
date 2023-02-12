<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Location;

class ContractorsModel extends Model
{
    use HasFactory;
    
    protected $table = "contractors";

    protected $fillable = [
        "name_contact",
        "address",
        "zip_code",
        "id_number",
        "company_name",
        "company_telephone",
        "email",
        "percentage",
        "fk_type_ids",
        "fk_location_city",
        "fk_user",
        "fk_contractor_state",
        "id_quickbooks",
        "created_at",
        "updated_at"
    ];

    public function state()
    {
        return $this->belongsTo(ContractorStatesModel::class, 'fk_contractor_state', 'id');
    }

    public function type_id()
    {
        return $this->belongsTo(TypeIdsModel::class, 'fk_type_ids', 'id');
    }
    
    public function location_city()
    {
        return $this->belongsTo(LocationModel::class, 'fk_location_city', 'id');
    }
}
