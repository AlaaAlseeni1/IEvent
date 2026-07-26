<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// الإدارات والتعريفات: العامة (null) + الخاصة بكل شركة
return new class extends Migration
{
    public function up(): void
    {
        foreach (['lookup_groups', 'lookups'] as $tableName) {
            if (!Schema::hasColumn($tableName, 'company_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('company_id')->nullable()
                          ->constrained('companies')->cascadeOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['lookup_groups', 'lookups'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropConstrainedForeignId('company_id');
            });
        }
    }
};
