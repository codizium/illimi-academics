<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('illimi_exam_answers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id');
            $table->uuid('exam_attempt_id');
            $table->uuid('question_id');
            $table->text('answer')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->decimal('marks_awarded', 6, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'exam_attempt_id']);
            $table->index(['organization_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('illimi_exam_answers');
    }
};
