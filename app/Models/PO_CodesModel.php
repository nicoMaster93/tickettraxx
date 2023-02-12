<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PO_CodesModel extends Model
{
    use HasFactory;
    protected $table = "p_o_codes";

    protected $fillable = [
        "code",
        "fk_pickup",
        "fk_deliver",
        "rate"
    ];

    public $timestamps = false;

    public function pickup()
    {
        return $this->belongsTo(PickupDeliverModel::class, 'fk_pickup', 'id');
    }

    public function deliver()
    {
        return $this->belongsTo(PickupDeliverModel::class, 'fk_deliver', 'id');
    }
    

}
