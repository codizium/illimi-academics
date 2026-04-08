<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('illimi_questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id');
            $table->uuid('question_bank_id');
            $table->uuid('subject_id');
            $table->enum('type', ['mcq', 'true_false', 'short_answer', 'code_snippet']);
            $table->text('content');
            $table->json('options')->nullable();
            $table->text('correct_answer');
            $table->text('explanation')->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->decimal('marks', 6, 2)->default(1);
            $table->json('tags')->nullable();
            $table->string('curriculum_ref')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'subject_id']);
            $table->index(['organization_id', 'question_bank_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('illimi_questions');
    }
};
