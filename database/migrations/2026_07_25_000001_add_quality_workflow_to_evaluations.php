<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// دورة عمل الجودة على التقييمات: إرسال → مراجعة → اعتماد/رفض بملاحظات → إعادة إرسال
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            if (!Schema::hasColumn('evaluations', 'title')) {
                $table->string('title')->nullable()->after('evaluator_id');
            }
            if (!Schema::hasColumn('evaluations', 'location_id')) {
                $table->foreignId('location_id')->nullable()->after('title')->constrained('locations')->nullOnDelete();
            }
            if (!Schema::hasColumn('evaluations', 'attachments')) {
                $table->json('attachments')->nullable()->after('criteria');
            }
            if (!Schema::hasColumn('evaluations', 'quality_notes')) {
                $table->text('quality_notes')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('evaluations', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->after('quality_notes')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('evaluations', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            }
            if (!Schema::hasColumn('evaluations', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('reviewed_at');
            }
        });

        // توسيع قيم الحالة: draft, submitted, under_review, approved, rejected
        // نحوّل العمود إلى string لدعم القيم الجديدة (يعمل على MySQL و SQLite)
        Schema::table('evaluations', function (Blueprint $table) {
            $table->string('status')->default('draft')->change();
        });
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            foreach (['location_id', 'reviewed_by'] as $fk) {
                if (Schema::hasColumn('evaluations', $fk)) {
                    $table->dropConstrainedForeignId($fk);
                }
            }
            foreach (['title', 'attachments', 'quality_notes', 'reviewed_at', 'submitted_at'] as $col) {
                if (Schema::hasColumn('evaluations', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
