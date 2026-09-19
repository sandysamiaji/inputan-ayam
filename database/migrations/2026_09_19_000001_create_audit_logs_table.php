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
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('user_name', 100)->nullable();
                $table->string('user_role', 50)->nullable();
                $table->string('action', 50); // CREATE, UPDATE, DELETE, RESTORE, LOGIN, LOGOUT
                $table->string('module', 50); // telur, pakan, mortalitas, karantina, bobot, obat, gudang, master, pengguna, auth
                $table->text('description'); // Penjelasan rinci aktivitas dalam bahasa Indonesia
                $table->string('model_type', 150)->nullable(); // e.g. App\Models\EggProduction
                $table->unsignedBigInteger('model_id')->nullable(); // ID record asli
                $table->string('table_name', 100)->nullable(); // e.g. egg_productions
                $table->longText('original_data')->nullable(); // JSON data asli saat dibuat / dihapus
                $table->longText('changes')->nullable(); // JSON perbedaan data sebelum dan sesudah update
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->boolean('is_restored')->default(false); // Penanda apakah data terhapus sudah direstore
                $table->timestamp('restored_at')->nullable();
                $table->unsignedBigInteger('restored_by')->nullable();
                $table->string('restored_by_name', 100)->nullable();
                $table->timestamps();

                $table->index(['action', 'module']);
                $table->index(['created_at']);
                $table->index(['is_restored']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
