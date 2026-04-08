<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    */
    'route_prefix' => env('ILLIMI_ACADEMICS_ROUTE_PREFIX', 'api/v1/academics'),

    /*
    |--------------------------------------------------------------------------
    | Default Grade Scale
    |--------------------------------------------------------------------------
    */
    'default_grade_scale' => env('ILLIMI_ACADEMICS_DEFAULT_GRADE_SCALE', 'standard'),

    /*
    |--------------------------------------------------------------------------
    | Pass Mark
    |--------------------------------------------------------------------------
    */
    'pass_mark' => (int) env('ILLIMI_ACADEMICS_PASS_MARK', 50),

    /*
    |--------------------------------------------------------------------------
    | Negative Marking
    |--------------------------------------------------------------------------
    */
    'negative_marking_enabled' => (bool) env('ILLIMI_ACADEMICS_NEGATIVE_MARKING_ENABLED', false),
    'negative_mark_value' => (float) env('ILLIMI_ACADEMICS_NEGATIVE_MARK_VALUE', 0.25),

    /*
    |--------------------------------------------------------------------------
    | Exam Settings
    |--------------------------------------------------------------------------
    */
    'exam_timeout_minutes' => (int) env('ILLIMI_ACADEMICS_EXAM_TIMEOUT_MINUTES', 120),
    'randomise_questions' => (bool) env('ILLIMI_ACADEMICS_RANDOMISE_QUESTIONS', true),
    'randomise_options' => (bool) env('ILLIMI_ACADEMICS_RANDOMISE_OPTIONS', true),
    'allow_review' => (bool) env('ILLIMI_ACADEMICS_ALLOW_REVIEW', true),

    /*
    |--------------------------------------------------------------------------
    | Transcript Settings
    |--------------------------------------------------------------------------
    */
    'transcript_cache_ttl' => (int) env('ILLIMI_ACADEMICS_TRANSCRIPT_CACHE_TTL', 86400),
    'transcript_signatory' => env('ILLIMI_ACADEMICS_TRANSCRIPT_SIGNATORY', ''),

    /*
    |--------------------------------------------------------------------------
    | Item Analysis
    |--------------------------------------------------------------------------
    */
    'item_analysis_enabled' => (bool) env('ILLIMI_ACADEMICS_ITEM_ANALYSIS_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Grade Components
    |--------------------------------------------------------------------------
    */
    'grade_components' => [
        'exam' => 'Exam',
        'continuous_assessment' => 'Continuous Assessment',
        'practical' => 'Practical',
        'project' => 'Project',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Weightings
    |--------------------------------------------------------------------------
    */
    'default_weightings' => [
        'exam' => 60,
        'continuous_assessment' => 30,
        'practical' => 10,
        'project' => 0,
    ],
];
