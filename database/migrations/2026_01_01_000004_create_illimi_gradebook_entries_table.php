<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        (!Schema::hasTable('illimi_gradebook_entries'))&&Schema::create('illimi_gradebook_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id');
            $table->uuid('student_id');
            $table->uuid('subject_id');
            $table->uuid('class_id');
            $table->string('academic_session');
            $table->string('term');
            $table->enum('component', ['exam', 'continuous_assessment', 'practical', 'project']);
            $table->decimal('score', 8, 2);
            $table->decimal('max_score', 8, 2);
            $table->decimal('weight', 6, 2);
            $table->uuid('entered_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('illimi_gradebook_entries');
    }
};
