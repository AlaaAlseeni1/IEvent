<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * حقول المتقدّم في طلبات التوظيف ديناميكية (تحددها كل شركة لوظيفتها)،
 * لذا يجب أن تكون كلها اختيارية على مستوى قاعدة البيانات حتى لا يفشل
 * التقديم عند عدم تفعيل حقل معيّن.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->string('full_name')->nullable()->change();
            $table->string('id_number')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->string('experience_years')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        // لا نعيد الإلزام لتفادي فشل السجلات ذات القيم الفارغة
    }
};
