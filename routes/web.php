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

Route::middleware(['web'])
    ->prefix('academics')
    ->name('academics.')
    ->group(function () {
        Route::get('/result-check', [ResultWebController::class, 'check'])->name('results.check');
        Route::get('/result-check/view', [ResultWebController::class, 'showCheck'])->name('results.check.view');
    });

Route::middleware(['web'])
    ->prefix('academics')
    ->name('academics.')
    ->group(function () {
        Route::get('/classes', [ClassWebController::class, 'index'])->name('classes.index');
        Route::get('/classrooms', [ClassroomWebController::class, 'index'])->name('classrooms.index');

        Route::get('/sections', [SectionWebController::class, 'index'])->name('sections.index');

        Route::get('/academic-years', [AcademicYearWebController::class, 'index'])->name('academic_years.index');

        Route::get('/terms', [AcademicTermWebController::class, 'index'])->name('terms.index');
        Route::get('/terms/add', [AcademicTermWebController::class, 'create'])->name('terms.create');
        Route::get('/terms/edit', [AcademicTermWebController::class, 'edit'])->name('terms.edit');

        Route::get('/subjects', [SubjectWebController::class, 'index'])->name('subjects.index');
        Route::get('/syllabi', [SyllabusWebController::class, 'index'])->name('syllabi.index');

        Route::get('/grade-scales', [GradeScaleWebController::class, 'index'])->name('grade_scales.index');

        Route::get('/gradebook', [GradebookWebController::class, 'index'])->name('gradebook.index');
        Route::get('/gradebook/add', [GradebookWebController::class, 'create'])->name('gradebook.create');

        Route::get('/results', [ResultWebController::class, 'index'])->name('results.index');
        Route::get('/results/publish', [ResultWebController::class, 'publish'])->name('results.publish');
        Route::get('/results/publish/manage', [ResultWebController::class, 'manage'])->name('results.publish.manage');

        Route::get('/transcripts', [TranscriptWebController::class, 'index'])->name('transcripts.index');

        Route::get('/question-banks', [QuestionBankWebController::class, 'index'])->name('question_banks.index');
        Route::get('/question-banks/add', [QuestionBankWebController::class, 'create'])->name('question_banks.create');
        Route::get('/questions', [QuestionWebController::class, 'index'])->name('questions.index');
        Route::get('/questions/add', [QuestionWebController::class, 'create'])->name('questions.create');

        Route::get('/exams', [ExamWebController::class, 'index'])->name('exams.index');
        Route::get('/exams/add', [ExamWebController::class, 'create'])->name('exams.create');
        Route::get('/exams/attempts', [ExamWebController::class, 'attempts'])->name('exams.attempts');
        Route::get('/exams/item-analysis', [ExamWebController::class, 'itemAnalysis'])->name('exams.item_analysis');

        Route::get('/appeals', [AppealWebController::class, 'index'])->name('appeals.index');
        Route::get('/appeals/new', [AppealWebController::class, 'create'])->name('appeals.create');
    });
