<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
        $table->id('vehicle_id'); // Primary Key
        $table->string('assured');
        $table->text('address')->nullable();
        $table->year('year_model');
        $table->string('make');
        $table->string('denomination');
        $table->string('color');
        
        // Unique Constraints
        $table->string('file_no')->unique(); // Also indexed
        $table->string('engine_no')->unique();
        $table->string('chassis_no')->unique();
        
        // Standard Index
        $table->string('plate_no')->index(); // Faster lookups
        
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
