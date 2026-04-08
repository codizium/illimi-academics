<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('illimi_exam_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id');
            $table->uuid('exam_id');
            $table->uuid('student_id');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->integer('time_taken_seconds')->nullable();
            $table->decimal('score', 8, 2)->nullable();
            $table->boolean('is_auto_graded')->default(false);
            $table->enum('status', ['in_progress', 'submitted', 'graded'])->default('in_progress');
            $table->string('ip_address')->nullable();
            $table->json('browser_info')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['exam_id', 'student_id']);
            $table->index(['organization_id', 'exam_id']);
            $table->index(['organization_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('illimi_exam_attempts');
    }
};
