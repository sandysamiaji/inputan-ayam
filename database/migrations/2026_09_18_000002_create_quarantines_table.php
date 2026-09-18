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
        if (!Schema::hasTable('quarantines')) {
            Schema::create('quarantines', function (Blueprint $table) {
                $table->id();
                $table->foreignId('flock_id')->nullable()->constrained('flocks')->nullOnDelete();
                $table->foreignId('coop_id')->nullable()->constrained('coops')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->date('date');
                $table->time('time')->nullable();
                $table->string('battery_number', 100)->nullable(); // Nomor baterai kandang asal / tujuan
                $table->integer('count')->default(1); // Jumlah ekor ayam
                $table->string('status', 50)->default('sakit'); // sakit / sembuh / mati
                $table->string('cause', 255)->nullable(); // Penyebab / diagnosa sakit
                $table->string('action_taken', 255)->nullable(); // Tindakan / obat yang diberikan
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quarantines');
    }
};
