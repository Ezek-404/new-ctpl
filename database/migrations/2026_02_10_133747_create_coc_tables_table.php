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
        Schema::create('coc_table', function (Blueprint $table) {
            $table->id('coc_id'); // This creates a BigIncrements (Unsigned Big Integer)
            $table->string('coc_no');
            $table->string('coc_type');
            $table->string('coc_status')->default('Available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coc_table');
    }
};
