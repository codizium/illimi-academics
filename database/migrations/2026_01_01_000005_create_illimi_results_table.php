<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('illimi_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id');
            $table->uuid('student_id');
            $table->uuid('class_id');
            $table->string('academic_session');
            $table->string('term');
            $table->decimal('total_score', 8, 2)->nullable();
            $table->string('grade')->nullable();
            $table->decimal('grade_point', 6, 2)->nullable();
            $table->string('remark')->nullable();
            $table->integer('position_in_class')->nullable();
            $table->enum('status', ['draft', 'under_review', 'published', 'archived'])->default('draft');
            $table->uuid('published_by')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('illimi_results');
    }
};
