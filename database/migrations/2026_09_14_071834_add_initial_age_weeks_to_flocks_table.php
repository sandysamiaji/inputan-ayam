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
        Schema::table('flocks', function (Blueprint $table) {
            // Usia ayam (dalam minggu) saat pertama kali diinputkan ke sistem
            $table->unsignedInteger('initial_age_weeks')->default(0)->after('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flocks', function (Blueprint $table) {
            $table->dropColumn('initial_age_weeks');
        });
    }
};
