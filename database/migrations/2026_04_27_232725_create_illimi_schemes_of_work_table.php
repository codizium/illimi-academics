<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('illimi_schemes_of_work', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->index();
            $table->uuid('syllabus_id')->index();
            $table->uuid('academic_year_id')->index();
            $table->uuid('academic_term_id')->index();
            $table->uuid('teacher_id')->nullable()->index();
            $table->uuid('class_id')->nullable()->index();
            
            $table->integer('week_number');
            $table->string('topic');
            $table->text('sub_topics')->nullable();
            $table->text('learning_objectives')->nullable();
            $table->text('teaching_aids')->nullable();
            $table->text('assessment_methods')->nullable();
            $table->text('remarks')->nullable();
            
            $table->enum('status', ['draft', 'review', 'approved'])->default('draft');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('syllabus_id')->references('id')->on('illimi_syllabi')->onDelete('cascade');
            $table->foreign('academic_year_id')->references('id')->on('illimi_academic_years')->onDelete('cascade');
            $table->foreign('academic_term_id')->references('id')->on('illimi_academic_terms')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('illimi_staff')->onDelete('set null');
            $table->foreign('class_id')->references('id')->on('illimi_classes')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('illimi_schemes_of_work');
    }
};
