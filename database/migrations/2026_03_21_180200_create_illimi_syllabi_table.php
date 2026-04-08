<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('illimi_syllabi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id');
            $table->foreignUuid('subject_id')->constrained('illimi_subjects')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->longText('objectives')->nullable();
            $table->longText('topics')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('illimi_syllabi');
    }
};
