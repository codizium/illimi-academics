<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('illimi_grade_scales', function (Blueprint $table) {
            if (! Schema::hasColumn('illimi_grade_scales', 'min_score')) {
                $table->decimal('min_score', 5, 2)->nullable()->after('code');
            }

            if (! Schema::hasColumn('illimi_grade_scales', 'max_score')) {
                $table->decimal('max_score', 5, 2)->nullable()->after('min_score');
            }
        });
    }

    public function down(): void
    {
        Schema::table('illimi_grade_scales', function (Blueprint $table) {
            if (Schema::hasColumn('illimi_grade_scales', 'max_score')) {
                $table->dropColumn('max_score');
            }

            if (Schema::hasColumn('illimi_grade_scales', 'min_score')) {
                $table->dropColumn('min_score');
            }
        });
    }
};
