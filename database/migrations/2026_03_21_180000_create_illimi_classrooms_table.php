<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('illimi_classrooms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id');
            $table->string('name');
            $table->string('code')->unique();
            $table->integer('capacity')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('illimi_classrooms');
    }
};
