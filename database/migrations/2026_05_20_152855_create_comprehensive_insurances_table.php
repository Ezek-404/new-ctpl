<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comprehensive_insurances', function (Blueprint $table) {
            $table->id(); // auto-incrementing primary key ID
            $table->string('policy_no')->unique();
            $table->string('soa_no')->nullable();
            $table->string('assured');
            $table->text('address')->nullable();
            $table->string('mortgagee')->nullable(); // Optional field now
            
            // Financial calculations values fields
            $table->decimal('value', 15, 2)->default(0.00);
            $table->decimal('rate', 5, 2)->default(0.00);
            $table->decimal('pd', 15, 2)->nullable();
            $table->decimal('bi', 15, 2)->nullable();
            
            // Vehicle specifications details fields
            $table->string('model')->nullable();
            $table->string('brand')->nullable();
            $table->string('type')->nullable();
            $table->string('color')->nullable();
            $table->string('plate_no')->nullable();
            $table->string('chassis_no')->nullable();
            $table->string('engine_no')->nullable();
            $table->string('file_no')->nullable();
            
            // Timestamps tracking (created_at, updated_at, deleted_at)
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comprehensive_insurances');
    }
};