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
    }
}
