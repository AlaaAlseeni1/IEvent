<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'start_date')) {
                $table->date('start_date')->nullable();
            }
            if (!Schema::hasColumn('employees', 'end_date')) {
                $table->date('end_date')->nullable();
            }
            if (!Schema::hasColumn('employees', 'contract_status')) {
                $table->string('contract_status')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'contract_status']);
        });
    }
};