<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('illimi_grade_appeals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id');
            $table->uuid('result_id');
            $table->uuid('student_id');
            $table->text('reason');
            $table->enum('status', ['submitted', 'under_review', 'resolved', 'rejected'])->default('submitted');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution')->nullable();
            $table->uuid('resolved_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'student_id']);
            $table->index(['organization_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('illimi_grade_appeals');
    }
};
