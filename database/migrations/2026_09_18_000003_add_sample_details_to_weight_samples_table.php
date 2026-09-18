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
        if (Schema::hasTable('weight_samples')) {
            Schema::table('weight_samples', function (Blueprint $table) {
                if (!Schema::hasColumn('weight_samples', 'battery_number')) {
                    $table->string('battery_number')->nullable()->after('coop_id');
                }
                if (!Schema::hasColumn('weight_samples', 'egg_weight_gram')) {
                    $table->decimal('egg_weight_gram', 6, 2)->nullable()->after('average_weight_kg');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('weight_samples')) {
            Schema::table('weight_samples', function (Blueprint $table) {
                if (Schema::hasColumn('weight_samples', 'egg_weight_gram')) {
                    $table->dropColumn('egg_weight_gram');
                }
                if (Schema::hasColumn('weight_samples', 'battery_number')) {
                    $table->dropColumn('battery_number');
                }
            });
        }
    }
};
