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

Route::prefix('api/v1/academics')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::get('classes', [ClassController::class, 'index'])->name('v1.academics.classes.index');
    Route::post('classes', [ClassController::class, 'store'])->name('v1.academics.classes.store');
    Route::get('classes/{id}', [ClassController::class, 'show'])->name('v1.academics.classes.show');
    Route::put('classes/{id}', [ClassController::class, 'update'])->name('v1.academics.classes.update');
    Route::delete('classes/{id}', [ClassController::class, 'destroy'])->name('v1.academics.classes.destroy');

    Route::get('sections', [SectionController::class, 'index'])->name('v1.academics.sections.index');
    Route::post('sections', [SectionController::class, 'store'])->name('v1.academics.sections.store');
    Route::get('sections/{id}', [SectionController::class, 'show'])->name('v1.academics.sections.show');
    Route::put('sections/{id}', [SectionController::class, 'update'])->name('v1.academics.sections.update');
    Route::delete('sections/{id}', [SectionController::class, 'destroy'])->name('v1.academics.sections.destroy');

    Route::get('classrooms', [ClassroomController::class, 'index'])->name('v1.academics.classrooms.index');
    Route::post('classrooms', [ClassroomController::class, 'store'])->name('v1.academics.classrooms.store');
    Route::get('classrooms/{id}', [ClassroomController::class, 'show'])->name('v1.academics.classrooms.show');
    Route::put('classrooms/{id}', [ClassroomController::class, 'update'])->name('v1.academics.classrooms.update');
    Route::delete('classrooms/{id}', [ClassroomController::class, 'destroy'])->name('v1.academics.classrooms.destroy');

    Route::get('syllabi', [SyllabusController::class, 'index'])->name('v1.academics.syllabi.index');
    Route::post('syllabi', [SyllabusController::class, 'store'])->name('v1.academics.syllabi.store');
    Route::get('syllabi/{id}', [SyllabusController::class, 'show'])->name('v1.academics.syllabi.show');
    Route::put('syllabi/{id}', [SyllabusController::class, 'update'])->name('v1.academics.syllabi.update');
    Route::delete('syllabi/{id}', [SyllabusController::class, 'destroy'])->name('v1.academics.syllabi.destroy');

    Route::get('academic-years', [AcademicYearController::class, 'index'])->name('v1.academics.academic_years.index');
    Route::post('academic-years', [AcademicYearController::class, 'store'])->name('v1.academics.academic_years.store');
    Route::get('academic-years/{id}', [AcademicYearController::class, 'show'])->name('v1.academics.academic_years.show');
    Route::put('academic-years/{id}', [AcademicYearController::class, 'update'])->name('v1.academics.academic_years.update');
    Route::delete('academic-years/{id}', [AcademicYearController::class, 'destroy'])->name('v1.academics.academic_years.destroy');
    Route::post('academic/years/change', [AcademicYearController::class, 'changeAcademicYear'])->name('v1.academics.academic_years.change');

    Route::get('terms', [AcademicTermController::class, 'index'])->name('v1.academics.terms.index');
    Route::post('terms', [AcademicTermController::class, 'store'])->name('v1.academics.terms.store');
    Route::get('terms/{id}', [AcademicTermController::class, 'show'])->name('v1.academics.terms.show');
    Route::put('terms/{id}', [AcademicTermController::class, 'update'])->name('v1.academics.terms.update');
    Route::delete('terms/{id}', [AcademicTermController::class, 'destroy'])->name('v1.academics.terms.destroy');
    Route::post('academic/terms/change', [AcademicTermController::class, 'changeAcademicTerm'])->name('v1.academics.terms.change');

    Route::get('subjects', [SubjectController::class, 'index'])->name('v1.academics.subjects.index');
    Route::post('subjects', [SubjectController::class, 'store'])->name('v1.academics.subjects.store');
    Route::get('subjects/{id}', [SubjectController::class, 'show'])->name('v1.academics.subjects.show');
    Route::put('subjects/{id}', [SubjectController::class, 'update'])->name('v1.academics.subjects.update');
    Route::delete('subjects/{id}', [SubjectController::class, 'destroy'])->name('v1.academics.subjects.destroy');

    Route::get('gradebook', [GradebookController::class, 'index'])->name('v1.academics.gradebook.index');
    Route::post('gradebook', [GradebookController::class, 'store'])->name('v1.academics.gradebook.store');
    Route::get('gradebook/{id}', [GradebookController::class, 'show'])->name('v1.academics.gradebook.show');
    Route::put('gradebook/{id}', [GradebookController::class, 'update'])->name('v1.academics.gradebook.update');
    Route::delete('gradebook/{id}', [GradebookController::class, 'destroy'])->name('v1.academics.gradebook.destroy');

    Route::get('grade-scales', [GradeScaleController::class, 'index'])->name('v1.academics.grade_scales.index');
    Route::post('grade-scales', [GradeScaleController::class, 'store'])->name('v1.academics.grade_scales.store');
    Route::get('grade-scales/{id}', [GradeScaleController::class, 'show'])->name('v1.academics.grade_scales.show');
    Route::put('grade-scales/{id}', [GradeScaleController::class, 'update'])->name('v1.academics.grade_scales.update');
    Route::delete('grade-scales/{id}', [GradeScaleController::class, 'destroy'])->name('v1.academics.grade_scales.destroy');

    Route::get('results', [ResultController::class, 'index'])->name('v1.academics.results.index');
    Route::get('results/publication-preview', [ResultController::class, 'publicationPreview'])->name('v1.academics.results.publication_preview');
    Route::get('results/{id}', [ResultController::class, 'show'])->name('v1.academics.results.show');
    Route::post('results/publish', [ResultController::class, 'publish'])->name('v1.academics.results.publish');
    Route::post('results/unpublish', [ResultController::class, 'unpublish'])->name('v1.academics.results.unpublish');

    Route::get('transcripts/{studentId}', [TranscriptController::class, 'show'])->name('v1.academics.transcripts.show');
    Route::post('transcripts/{studentId}/generate', [TranscriptController::class, 'generate'])->name('v1.academics.transcripts.generate');

    Route::get('question-banks', [QuestionBankController::class, 'index'])->name('v1.academics.question_banks.index');
    Route::post('question-banks', [QuestionBankController::class, 'store'])->name('v1.academics.question_banks.store');
    Route::get('question-banks/{id}', [QuestionBankController::class, 'show'])->name('v1.academics.question_banks.show');
    Route::put('question-banks/{id}', [QuestionBankController::class, 'update'])->name('v1.academics.question_banks.update');
    Route::delete('question-banks/{id}', [QuestionBankController::class, 'destroy'])->name('v1.academics.question_banks.destroy');
    Route::get('questions', [QuestionController::class, 'index'])->name('v1.academics.questions.index');
    Route::post('questions', [QuestionController::class, 'store'])->name('v1.academics.questions.store');
    Route::get('questions/{id}', [QuestionController::class, 'show'])->name('v1.academics.questions.show');
    Route::put('questions/{id}', [QuestionController::class, 'update'])->name('v1.academics.questions.update');
    Route::delete('questions/{id}', [QuestionController::class, 'destroy'])->name('v1.academics.questions.destroy');

    Route::get('exams', [ExamController::class, 'index'])->name('v1.academics.exams.index');
    Route::post('exams', [ExamController::class, 'store'])->name('v1.academics.exams.store');
    Route::get('exams/{id}', [ExamController::class, 'show'])->name('v1.academics.exams.show');
    Route::put('exams/{id}', [ExamController::class, 'update'])->name('v1.academics.exams.update');
    Route::delete('exams/{id}', [ExamController::class, 'destroy'])->name('v1.academics.exams.destroy');
    Route::post('exams/{id}/start', [ExamController::class, 'start'])->name('v1.academics.exams.start');
    Route::post('exams/{id}/submit', [ExamController::class, 'submit'])->name('v1.academics.exams.submit');
    Route::get('exams/{id}/results', [ExamController::class, 'results'])->name('v1.academics.exams.results');
    Route::get('exams/{id}/item-analysis', [ExamController::class, 'itemAnalysis'])->name('v1.academics.exams.item_analysis');

    Route::get('exam-attempts', [ExamAttemptController::class, 'index'])->name('v1.academics.exam_attempts.index');

    Route::get('appeals', [GradeAppealController::class, 'index'])->name('v1.academics.appeals.index');
    Route::post('appeals', [GradeAppealController::class, 'store'])->name('v1.academics.appeals.store');
    Route::get('appeals/{id}', [GradeAppealController::class, 'show'])->name('v1.academics.appeals.show');
    Route::put('appeals/{id}/resolve', [GradeAppealController::class, 'resolve'])->name('v1.academics.appeals.resolve');
    Route::delete('appeals/{id}', [GradeAppealController::class, 'destroy'])->name('v1.academics.appeals.destroy');
});
