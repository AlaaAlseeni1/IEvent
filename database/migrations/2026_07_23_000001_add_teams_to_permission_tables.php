<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * تفعيل أدوار مستقلة لكل شركة (Spatie Teams) على قواعد البيانات القائمة.
 *
 * على تثبيت جديد: ترحيل Spatie الأساسي ينشئ البنية كاملة (لأن teams=true)،
 * لذا يتخطى هذا الترحيل نفسه. أما القواعد التي أُنشئت قبل تفعيل teams فيضيف
 * إليها العمود company_id ويعيد بناء جداول الربط مع ترحيل الإسنادات الحالية.
 */
return new class extends Migration
{
    public function up(): void
    {
        // البنية موجودة مسبقاً (تثبيت جديد بـ teams=true) → لا حاجة لأي تغيير
        if (Schema::hasColumn('roles', 'company_id')) {
            return;
        }

        Schema::table('roles', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('id');
            $table->index('company_id', 'roles_company_id_index');
        });
        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique('roles_name_guard_name_unique');
            $table->unique(['company_id', 'name', 'guard_name'], 'roles_company_name_guard_unique');
        });

        $this->rebuildPivot('model_has_roles', 'role_id', 'roles');
        $this->rebuildPivot('model_has_permissions', 'permission_id', 'permissions');

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function rebuildPivot(string $tableName, string $foreignKey, string $foreignTable): void
    {
        if (Schema::hasColumn($tableName, 'company_id')) {
            return;
        }

        $rows = DB::table($tableName)->get();

        Schema::drop($tableName);

        Schema::create($tableName, function (Blueprint $table) use ($foreignKey, $foreignTable, $tableName) {
            $table->unsignedBigInteger('company_id')->default(0);
            $table->unsignedBigInteger($foreignKey);
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->index(['model_id', 'model_type'], $tableName . '_model_id_model_type_index');
            $table->index('company_id', $tableName . '_company_id_index');
            $table->foreign($foreignKey)->references('id')->on($foreignTable)->cascadeOnDelete();
            $table->primary(['company_id', $foreignKey, 'model_id', 'model_type'], $tableName . '_pkey');
        });

        foreach ($rows as $row) {
            $companyId = 0;
            if ($row->model_type === \App\Models\User::class) {
                $companyId = DB::table('users')->where('id', $row->model_id)->value('company_id') ?? 0;
            }

            DB::table($tableName)->insert([
                'company_id' => $companyId,
                $foreignKey  => $row->{$foreignKey},
                'model_type' => $row->model_type,
                'model_id'   => $row->model_id,
            ]);
        }
    }

    public function down(): void
    {
        // لا تُعكَس على التثبيت الجديد (البنية جزء من ترحيل Spatie الأساسي)
        if (!Schema::hasColumn('roles', 'company_id')) {
            return;
        }
        // نترك البنية كما هي لتفادي فقدان الإسنادات؛ العكس اليدوي غير مدعوم هنا.
    }
};
