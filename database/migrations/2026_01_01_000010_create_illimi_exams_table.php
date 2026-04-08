<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        (!Schema::hasTable('illimi_exams')) && Schema::create('illimi_exams', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id');
            $table->string('title');
            $table->uuid('subject_id');
            $table->uuid('class_id');
            $table->string('academic_session');
            $table->string('term');
            $table->integer('duration_minutes');
            $table->decimal('total_marks', 8, 2);
            $table->decimal('pass_mark', 8, 2)->nullable();
            $table->boolean('negative_marking')->default(false);
            $table->decimal('negative_mark_value', 6, 2)->nullable();
            $table->boolean('randomise_questions')->default(true);
            $table->boolean('randomise_options')->default(true);
            $table->boolean('allow_review')->default(true);
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->uuid('created_by')->nullable();
            $table->json('proctoring_options')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('illimi_exams');
    }
};
