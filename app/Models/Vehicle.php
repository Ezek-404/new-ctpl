<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $table = 'vehicles';
    protected $primaryKey = 'vehicle_id'; // Important: Laravel needs to know this isn't 'id'
    public $incrementing = true;

    protected $fillable = [
        'assured', 'address', 'year_model', 'make', 'color', 
        'plate_no', 'file_no', 'engine_no', 'chassis_no', 'denomination'
    ];

    public function issuances()
    {
        return $this->hasMany(CtplIssuance::class, 'vehicle_id', 'vehicle_id');
    }
}
