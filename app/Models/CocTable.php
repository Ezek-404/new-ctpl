<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CocTable extends Model
{
    // Explicitly define the table name
    protected $table = 'coc_table';

    // UPDATE: Set the primary key to match your database change
    protected $primaryKey = 'coc_id';

    // Mass assignable attributes
    protected $fillable = [
        'coc_no',
        'coc_type',
        'coc_status',
    ];

    /**
     * Relationship with the Issuance (if needed for the transactions page)
     */
    public function issuance()
    {
        // Links to the issuance table using 'coc_id' as the foreign key
        return $this->hasOne(CtplIssuance::class, 'coc_id', 'coc_id');
    }
}