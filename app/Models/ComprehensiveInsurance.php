<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComprehensiveInsurance extends Model
{
    use HasFactory, SoftDeletes;

    // Directing to the table name (Laravel defaults plural, this ensures an exact link)
    protected $table = 'comprehensive_insurances';

    /**
     * The attributes that are mass assignable.
     * These precisely match your table headers layout.
     */
    protected $fillable = [
        'policy_no',
        'soa_no',
        'assured',
        'address',
        'mortgagee',
        'value',
        'rate',
        'pd',
        'bi',
        'model',
        'brand',
        'type',
        'color',
        'plate_no',
        'chassis_no',
        'engine_no',
        'file_no',
    ];

    /**
     * Typecasting data formatting types automatically when reading/writing from DB
     */
    protected $casts = [
        'value' => 'decimal:2',
        'rate' => 'decimal:2',
        'pd' => 'decimal:2',
        'bi' => 'decimal:2',
    ];
}