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
        Schema::create('ctpl_issuances', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->string('policy_no');
            $table->string('agent')->nullable();
            
            // Use foreignId to ensure the type matches coc_table's ID exactly
            $table->foreignId('coc_id')->constrained('coc_table', 'coc_id')->onDelete('cascade');
            $table->unsignedBigInteger('vehicle_id'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ctpl_issuances');
    }
};
