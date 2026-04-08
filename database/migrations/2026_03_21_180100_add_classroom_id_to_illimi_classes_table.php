<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('illimi_classes', function (Blueprint $table) {
            if (!Schema::hasColumn('illimi_classes', 'classroom_id')) {
                $table->foreignUuid('classroom_id')->nullable()->after('capacity')->constrained('illimi_classrooms')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('illimi_classes', function (Blueprint $table) {
            if (Schema::hasColumn('illimi_classes', 'classroom_id')) {
                $table->dropForeign(['classroom_id']);
                $table->dropColumn('classroom_id');
            }
        });
    }
};
