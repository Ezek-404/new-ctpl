<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CtplIssuance extends Model
{
    // Define the table name
    protected $table = 'ctpl_issuances';

    // Set the custom primary key
    protected $primaryKey = 'transaction_id';

    // Allow mass assignment for these fields
    protected $fillable = [
        'agent',
        'policy_no',
        'amount',
        'coc_id',      // UPDATE 1: Renamed from 'id' to 'coc_id'
        'vehicle_id',
    ];

    /**
     * Relationship with the Vehicle model
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'vehicle_id');
    }

    /**
     * Relationship with the Coc model
     */
    public function coc()
    {
        // UPDATE 2: Link 'coc_id' in this table to 'coc_id' in coc_table
        return $this->belongsTo(CocTable::class, 'coc_id', 'coc_id');
    }
}