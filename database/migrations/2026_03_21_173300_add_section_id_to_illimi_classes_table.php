<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('illimi_classes', function (Blueprint $table) {
            $table->foreignUuid('section_id')->nullable()->after('section')->constrained('illimi_sections')->nullOnDelete();
            $table->dropColumn('section');
        });
    }

    public function down(): void
    {
        Schema::table('illimi_classes', function (Blueprint $table) {
            $table->string('section')->nullable()->after('level');
            $table->dropForeign(['section_id']);
            $table->dropColumn('section_id');
        });
    }
};
