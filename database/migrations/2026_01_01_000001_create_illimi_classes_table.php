<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('illimi_classes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id');
            $table->string('name');
            $table->string('level');
            $table->string('section')->nullable();
            $table->uuid('class_teacher_id')->nullable();
            $table->integer('capacity')->default(40);
            $table->string('academic_session');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'academic_session']);
            $table->index(['organization_id', 'level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('illimi_classes');
    }
};
