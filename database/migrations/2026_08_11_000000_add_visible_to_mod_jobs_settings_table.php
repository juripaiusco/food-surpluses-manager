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
        Schema::table('mod_jobs_settings', function (Blueprint $table) {
            $table->boolean('visible')->default(true)->after('dynamic');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mod_jobs_settings', function (Blueprint $table) {
            $table->dropColumn('visible');
        });
    }
};
