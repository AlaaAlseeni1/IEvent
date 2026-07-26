<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// إحداثيات الموقع (خط العرض/الطول) — يستخدمها الفورم ورابط خرائط جوجل
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            if (!Schema::hasColumn('locations', 'lat')) {
                $table->decimal('lat', 10, 7)->nullable()->after('address');
            }
            if (!Schema::hasColumn('locations', 'lng')) {
                $table->decimal('lng', 10, 7)->nullable()->after('lat');
            }
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn(['lat', 'lng']);
        });
    }
};
