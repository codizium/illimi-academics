<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('illimi_subjects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id');
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_compulsory')->default(false);
            $table->integer('credit_units')->default(3);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('illimi_subjects');
    }
};
