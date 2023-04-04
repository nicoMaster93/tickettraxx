<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FscModel extends Model
{
    use HasFactory;
    protected $table = "surcharge";

    protected $fillable = [
        "from",
        "to",
        "percentaje",
        "fk_customer"
    ];

    public $timestamps = false;

    public function customer()
    {
        return $this->belongsTo(CustomerModel::class, 'fk_customer', 'id');
    }

}
