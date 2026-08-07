<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mod_jobs_settings', function (Blueprint $table) {
            $table->json('json_modules')->nullable()->after('dynamic');
        });

        // Retrocompatibilità: le sezioni esistenti erano visibili
        // incondizionatamente solo nel modulo Ascolto (jobs_listen).
        DB::table('mod_jobs_settings')
            ->where('type', 'section')
            ->update(['json_modules' => json_encode(['jobs_listen' => true])]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mod_jobs_settings', function (Blueprint $table) {
            $table->dropColumn('json_modules');
        });
    }
};
