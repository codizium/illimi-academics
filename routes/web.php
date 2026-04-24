<?php

use Illimi\Academics\Controllers\Web\AcademicTermWebController;
use Illimi\Academics\Controllers\Web\AcademicYearWebController;
use Illimi\Academics\Controllers\Web\AppealWebController;
use Illimi\Academics\Controllers\Web\ClassWebController;
use Illimi\Academics\Controllers\Web\ClassroomWebController;
use Illimi\Academics\Controllers\Web\ExamWebController;
use Illimi\Academics\Controllers\Web\GradeScaleWebController;
use Illimi\Academics\Controllers\Web\GradebookWebController;
use Illimi\Academics\Controllers\Web\QuestionBankWebController;
use Illimi\Academics\Controllers\Web\QuestionWebController;
use Illimi\Academics\Controllers\Web\ResultWebController;
use Illimi\Academics\Controllers\Web\SectionWebController;
use Illimi\Academics\Controllers\Web\SubjectWebController;
use Illimi\Academics\Controllers\Web\SyllabusWebController;
use Illimi\Academics\Controllers\Web\TranscriptWebController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'organization'])
    ->prefix('academics')
    ->name('academics.')
    ->group(function () {
        Route::get('/result-check', [ResultWebController::class, 'check'])->name('results.check');
        Route::get('/result-check/view', [ResultWebController::class, 'showCheck'])->name('results.check.view');
    });

Route::middleware(['web', 'auth', 'organization'])
    ->prefix('academics')
    ->name('academics.')
    ->group(function () {
        Route::get('/settings', [\Illimi\Academics\Controllers\Web\AcademicSettingsWebController::class, 'index'])->name('settings');
        Route::resource('classes', ClassWebController::class)->only(['index']);
        Route::resource('classrooms', ClassroomWebController::class)->only(['index']);
        Route::resource('sections', SectionWebController::class)->only(['index']);
        Route::resource('academic-years', AcademicYearWebController::class)
            ->only(['index'])
            ->names('academic_years');
        Route::resource('terms', AcademicTermWebController::class)->only(['index', 'create', 'edit']);
        Route::resource('subjects', SubjectWebController::class)->only(['index']);
        Route::resource('syllabi', SyllabusWebController::class)->only(['index']);
        Route::resource('grade-scales', GradeScaleWebController::class)
            ->only(['index'])
            ->names('grade_scales');
        Route::resource('gradebook', GradebookWebController::class)->only(['index', 'create']);
        Route::resource('results', ResultWebController::class)->only(['index']);
        Route::get('/results/publish', [ResultWebController::class, 'publish'])->name('results.publish');
        Route::get('/results/publish/manage', [ResultWebController::class, 'manage'])->name('results.publish.manage');
        Route::resource('transcripts', TranscriptWebController::class)->only(['index']);
        Route::resource('question-banks', QuestionBankWebController::class)
            ->only(['index', 'create'])
            ->names('question_banks');
        Route::resource('questions', QuestionWebController::class)->only(['index', 'create']);
        Route::resource('exams', ExamWebController::class)->only(['index', 'create']);
        Route::get('/exams/attempts', [ExamWebController::class, 'attempts'])->name('exams.attempts');
        Route::get('/exams/item-analysis', [ExamWebController::class, 'itemAnalysis'])->name('exams.item_analysis');
        Route::resource('appeals', AppealWebController::class)->only(['index', 'create']);
    });
