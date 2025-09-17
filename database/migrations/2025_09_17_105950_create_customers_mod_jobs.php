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
        Schema::create('customers_mod_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')
                ->constrained()
                ->onDelete('cascade');
            $table->json('schema')->nullable(); // definizione dei campi dinamici
            $table->json('values')->nullable(); // valori compilati
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers_mod_jobs');
    }
};
