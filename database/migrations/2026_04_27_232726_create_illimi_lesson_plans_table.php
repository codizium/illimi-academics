<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('illimi_lesson_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->index();
            $table->uuid('scheme_of_work_id')->index();
            $table->uuid('teacher_id')->nullable()->index();
            
            $table->date('date');
            $table->integer('duration_minutes')->default(40);
            $table->string('topic');
            $table->text('learning_outcomes')->nullable();
            $table->text('introduction')->nullable();
            $table->text('presentation_steps')->nullable();
            $table->text('evaluation')->nullable();
            $table->text('conclusion')->nullable();
            $table->text('teaching_aids')->nullable();
            
            $table->enum('status', ['draft', 'ready', 'completed'])->default('draft');
            $table->boolean('is_completed')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('scheme_of_work_id')->references('id')->on('illimi_schemes_of_work')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('illimi_staff')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('illimi_lesson_plans');
    }
};
