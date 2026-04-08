<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('illimi_transcripts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id');
            $table->uuid('student_id');
            $table->string('academic_session');
            $table->string('term')->nullable();
            $table->string('file_path')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('illimi_transcripts');
    }
};
