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
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('user_name', 100)->nullable();
                $table->string('user_role', 50)->nullable();
                $table->string('action', 50)->default('GENERAL');
                $table->string('module', 50)->default('umum');
                $table->text('description')->nullable();
                $table->string('model_type', 150)->nullable();
                $table->unsignedBigInteger('model_id')->nullable();
                $table->string('table_name', 100)->nullable();
                $table->longText('original_data')->nullable();
                $table->longText('changes')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->boolean('is_restored')->default(false);
                $table->timestamp('restored_at')->nullable();
                $table->unsignedBigInteger('restored_by')->nullable();
                $table->string('restored_by_name', 100)->nullable();
                $table->timestamps();

                $table->index(['action', 'module']);
                $table->index(['created_at']);
                $table->index(['is_restored']);
            });
            return;
        }

        // Jika tabel sudah ada, periksa setiap kolom dan tambahkan jika belum ada
        Schema::table('audit_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('audit_logs', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('audit_logs', 'user_name')) {
                $table->string('user_name', 100)->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'user_role')) {
                $table->string('user_role', 50)->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'action')) {
                $table->string('action', 50)->default('GENERAL');
            }
            if (!Schema::hasColumn('audit_logs', 'module')) {
                $table->string('module', 50)->default('umum');
            }
            if (!Schema::hasColumn('audit_logs', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'model_type')) {
                $table->string('model_type', 150)->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'model_id')) {
                $table->unsignedBigInteger('model_id')->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'table_name')) {
                $table->string('table_name', 100)->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'original_data')) {
                $table->longText('original_data')->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'changes')) {
                $table->longText('changes')->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'ip_address')) {
                $table->string('ip_address', 45)->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'user_agent')) {
                $table->text('user_agent')->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'is_restored')) {
                $table->boolean('is_restored')->default(false);
            }
            if (!Schema::hasColumn('audit_logs', 'restored_at')) {
                $table->timestamp('restored_at')->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'restored_by')) {
                $table->unsignedBigInteger('restored_by')->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'restored_by_name')) {
                $table->string('restored_by_name', 100)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No destructive reverse
    }
};
