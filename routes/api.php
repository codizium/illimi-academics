<?php

use Illimi\Academics\Controllers\V1\AcademicTermController;
use Illimi\Academics\Controllers\V1\ClassroomController;
use Illimi\Academics\Controllers\V1\SectionController;
use Illimi\Academics\Controllers\V1\SyllabusController;
use Illimi\Academics\Controllers\V1\AcademicYearController;
use Illimi\Academics\Controllers\V1\ClassController;
use Illimi\Academics\Controllers\V1\ExamAttemptController;
use Illimi\Academics\Controllers\V1\ExamController;
use Illimi\Academics\Controllers\V1\GradeAppealController;
use Illimi\Academics\Controllers\V1\GradeScaleController;
use Illimi\Academics\Controllers\V1\GradebookController;
use Illimi\Academics\Controllers\V1\QuestionBankController;
use Illimi\Academics\Controllers\V1\QuestionController;
use Illimi\Academics\Controllers\V1\ResultController;
use Illimi\Academics\Controllers\V1\SubjectController;
use Illimi\Academics\Controllers\V1\TranscriptController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/academics')->middleware(['api', 'auth:sanctum', 'organization'])->group(function () {
    Route::apiResource('classes', ClassController::class)->names('v1.academics.classes');
    Route::apiResource('sections', SectionController::class)->names('v1.academics.sections');
    Route::apiResource('classrooms', ClassroomController::class)->names('v1.academics.classrooms');
    Route::apiResource('syllabi', SyllabusController::class)->names('v1.academics.syllabi');
    Route::apiResource('academic-years', AcademicYearController::class)->names('v1.academics.academic_years');
    Route::post('academic/years/change', [AcademicYearController::class, 'changeAcademicYear'])->name('v1.academics.academic_years.change');
    Route::apiResource('terms', AcademicTermController::class)->names('v1.academics.terms');
    Route::post('academic/terms/change', [AcademicTermController::class, 'changeAcademicTerm'])->name('v1.academics.terms.change');
    Route::apiResource('subjects', SubjectController::class)->names('v1.academics.subjects');
    Route::apiResource('gradebook', GradebookController::class)->names('v1.academics.gradebook');
    Route::apiResource('grade-scales', GradeScaleController::class)->names('v1.academics.grade_scales');
    Route::apiResource('results', ResultController::class)->only(['index', 'show'])->names('v1.academics.results');
    Route::get('results/publication-preview', [ResultController::class, 'publicationPreview'])->name('v1.academics.results.publication_preview');
    Route::post('results/publish', [ResultController::class, 'publish'])->name('v1.academics.results.publish');
    Route::post('results/unpublish', [ResultController::class, 'unpublish'])->name('v1.academics.results.unpublish');

    Route::apiResource('transcripts', TranscriptController::class)
        ->only(['show'])
        ->parameters(['transcripts' => 'studentId'])
        ->names('v1.academics.transcripts');
    Route::post('transcripts/{studentId}/generate', [TranscriptController::class, 'generate'])->name('v1.academics.transcripts.generate');
    Route::apiResource('question-banks', QuestionBankController::class)->names('v1.academics.question_banks');
    Route::apiResource('questions', QuestionController::class)->names('v1.academics.questions');
    Route::apiResource('exams', ExamController::class)->names('v1.academics.exams');
    Route::post('exams/{id}/start', [ExamController::class, 'start'])->name('v1.academics.exams.start');
    Route::post('exams/{id}/submit', [ExamController::class, 'submit'])->name('v1.academics.exams.submit');
    Route::get('exams/{id}/results', [ExamController::class, 'results'])->name('v1.academics.exams.results');
    Route::get('exams/{id}/item-analysis', [ExamController::class, 'itemAnalysis'])->name('v1.academics.exams.item_analysis');
    Route::apiResource('exam-attempts', ExamAttemptController::class)
        ->only(['index'])
        ->names('v1.academics.exam_attempts');
    Route::apiResource('appeals', GradeAppealController::class)
        ->only(['index', 'store', 'show', 'destroy'])
        ->names('v1.academics.appeals');
    Route::put('appeals/{id}/resolve', [GradeAppealController::class, 'resolve'])->name('v1.academics.appeals.resolve');
});
