<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('illimi_subjects', function (Blueprint $table) {
            if (! Schema::hasColumn('illimi_subjects', 'syllabus')) {
                $table->longText('syllabus')->nullable()->after('description');
            }

            $table->integer('credit_units')->nullable()->default(null)->change();
        });

        Schema::create('illimi_staff_subject', function (Blueprint $table) {
            $table->id();
            $table->uuid('organization_id');
            $table->uuid('staff_id');
            $table->uuid('subject_id');
            $table->timestamps();

            $table->unique(['organization_id', 'staff_id', 'subject_id'], 'illimi_staff_subject_unique');
            $table->index(['organization_id', 'subject_id']);
            $table->index(['organization_id', 'staff_id']);
        });

        Schema::create('illimi_class_subject', function (Blueprint $table) {
            $table->id();
            $table->uuid('organization_id');
            $table->uuid('class_id');
            $table->uuid('subject_id');
            $table->timestamps();

            $table->unique(['organization_id', 'class_id', 'subject_id'], 'illimi_class_subject_unique');
            $table->index(['organization_id', 'subject_id']);
            $table->index(['organization_id', 'class_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('illimi_class_subject');
        Schema::dropIfExists('illimi_staff_subject');

        Schema::table('illimi_subjects', function (Blueprint $table) {
            if (Schema::hasColumn('illimi_subjects', 'syllabus')) {
                $table->dropColumn('syllabus');
            }

            $table->integer('credit_units')->default(3)->nullable(false)->change();
        });
    }
};
