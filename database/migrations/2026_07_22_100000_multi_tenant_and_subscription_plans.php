<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // الجداول التي تصبح معزولة لكل شركة (Multi-Tenant)
    private array $tenantTables = [
        'employees', 'contracts', 'attendance', 'tasks', 'assets',
        'locations', 'events', 'regions', 'teams', 'shifts', 'visits',
        'evaluations', 'support_tickets', 'job_openings', 'job_applications',
        'readiness_licenses', 'surveys',
    ];

    public function up(): void
    {
        // الباقات تتحول إلى خطط اشتراك بمدة محددة
        Schema::table('packages', function (Blueprint $table) {
            $table->string('type')->default('monthly')->after('name'); // weekly|monthly|quarterly|yearly
        });

        // بيانات الدفع في الاشتراك + حالة "معلق"
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('price');
            $table->date('paid_at')->nullable()->after('payment_method');
        });
        // توسيع قيم الحالة لتشمل suspended (إعادة تعريف القيد في SQLite/MySQL عبر عمود نصي)
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('status')->default('active')->change();
        });

        foreach ($this->tenantTables as $tableName) {
            if (!Schema::hasColumn($tableName, 'company_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('company_id')->nullable()
                          ->constrained('companies')->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tenantTables as $tableName) {
            if (Schema::hasColumn($tableName, 'company_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropConstrainedForeignId('company_id');
                });
            }
        }

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'paid_at']);
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
