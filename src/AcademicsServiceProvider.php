<?php

namespace Illimi\Academics;

use Illimi\Academics\Console\AnalyseExamItemsCommand;
use Illimi\Academics\Console\BackfillSubjectClassAssignmentsCommand;
use Illimi\Academics\Console\PublishResultsCommand;
use Illimi\Academics\Events\GradeAppealSubmitted;
use Illimi\Academics\Events\ResultsPublished;
use Illimi\Academics\Listeners\NotifyStudentsOnResultPublish;
use Illimi\Academics\Listeners\NotifyTeacherOnAppeal;
use Illimi\Academics\Listeners\TriggerTranscriptGeneration;
use Illimi\Academics\Models\Exam;
use Illimi\Academics\Models\Result;
use Illimi\Academics\Policies\ExamPolicy;
use Illimi\Academics\Policies\ResultPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AcademicsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/illimi-academics.php', 'illimi-academics');

        $this->app->singleton('illimi-academics', function () {
            return new IllimiAcademics();
        });
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'illimi-academics');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'illimi-academics');

        $this->publishes([
            __DIR__.'/../config/illimi-academics.php' => config_path('illimi-academics.php'),
        ], 'illimi-academics-config');

        Gate::policy(Result::class, ResultPolicy::class);
        Gate::policy(Exam::class, ExamPolicy::class);

        Event::listen(ResultsPublished::class, NotifyStudentsOnResultPublish::class);
        Event::listen(ResultsPublished::class, TriggerTranscriptGeneration::class);
        Event::listen(GradeAppealSubmitted::class, NotifyTeacherOnAppeal::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                PublishResultsCommand::class,
                AnalyseExamItemsCommand::class,
                BackfillSubjectClassAssignmentsCommand::class,
            ]);
        }

        $this->registerMenu();
    }

    protected function registerMenu()
    {
        if (class_exists(\Illimi\IllimiCore\Facades\IllimiCore::class)) {
            $nav = \Illimi\IllimiCore\Facades\IllimiCore::navigation();

            // Classes & Academics
            $nav->register('classes', [
                'label' => 'Classes',
                'icon' => 'ri-list-view',
                'category' => 'academics',
                'priority' => 20,
                'roles' => ['admin', 'super-admin'],
                'feature' => 'class_management',
                'children' => [
                    ['label' => 'Sections', 'route' => 'academics.sections.index'],
                    ['label' => 'Classes', 'route' => 'academics.classes.index'],
                    ['label' => 'Classrooms', 'route' => 'academics.classrooms.index'],
                    ['label' => 'Subjects', 'route' => 'academics.subjects.index'],
                    ['label' => 'Syllabi', 'route' => 'academics.syllabi.index'],
                ],
            ]);

            // Examinations
            $nav->register('exams', [
                'label' => 'Examinations',
                'icon' => 'ri-file-edit-line',
                'category' => 'academics',
                'priority' => 30,
                'roles' => ['admin', 'super-admin'],
                'feature' => 'exam_management',
                'children' => [
                    ['label' => 'Exams', 'route' => 'academics.exams.index'],
                    ['label' => 'Exam Results', 'route' => 'academics.results.index'],
                    ['label' => 'Publish Results', 'route' => 'academics.results.publish'],
                    ['label' => 'Transcripts', 'route' => 'academics.transcripts.index'],
                ],
            ]);

            // Teacher Specific Academics (If not admin)
            $nav->register('teacher-academics', [
                'label' => 'Academics',
                'icon' => 'ri-mortarboard-line',
                'category' => 'academics',
                'priority' => 21,
                'roles' => ['teacher'],
                'children' => [
                    ['label' => 'Subjects', 'route' => 'academics.subjects.index'],
                    ['label' => 'Syllabi', 'route' => 'academics.syllabi.index'],
                ],
            ]);
        }
    }
}
