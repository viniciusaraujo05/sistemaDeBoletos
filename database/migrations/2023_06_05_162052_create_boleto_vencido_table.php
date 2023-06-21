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
        Schema::create('boletosVencidos', function (Blueprint $table) {
            $table->id();
            $table->decimal('original_amount');
            $table->decimal('amount', 8, 2);
            $table->string('due_date');
            $table->string('payment_date');
            $table->decimal('interest_amount_calculated', 8, 2);
            $table->decimal('fine_amount_calculated', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boletosVencidos');
    }
};
