<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationModel extends Model
{
    use HasFactory;
    protected $table = "location";

    protected $fillable = [
        "location_name",
        "location_type",
        "fk_location"
    ];

    public $timestamps = false;

    public function location_sup()
    {
        return $this->belongsTo(LocationModel::class, 'fk_location', 'id');
    }
}
