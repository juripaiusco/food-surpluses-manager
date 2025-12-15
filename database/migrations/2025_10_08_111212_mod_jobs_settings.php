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
        Schema::create('mod_jobs_settings', function (Blueprint $table) {
            $table->id();

            $table->char('type', 255);
            $table->char('title', 255);
            $table->json('schema')->nullable();
            $table->char('dynamic', 1)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mod_jobs_settings');
    }
};
