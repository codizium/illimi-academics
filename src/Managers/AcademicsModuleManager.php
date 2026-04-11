<?php

namespace Illimi\Academics\Managers;

class AcademicsModuleManager
{
    public function sideMenu(): array
    {
        return [
            [
                'label' => 'Classes',
                'icon' => 'ri-list-view',
                'route' => 'javascript:void(0)',
                'roles' => ['admin'],
                'children' => [
                    ['label' => 'Sections', 'route' => 'academics.sections.index'],
                    ['label' => 'Classes', 'route' => 'academics.classes.index'],
                    ['label' => 'Classrooms', 'route' => 'academics.classrooms.index'],
                    ['label' => 'Subjects', 'route' => 'academics.subjects.index'],
                    ['label' => 'Syllabi', 'route' => 'academics.syllabi.index'],
                ],
            ],
            [
                'label' => 'Examinations',
                'icon' => 'ri-file-edit-line',
                'route' => 'javascript:void(0)',
                'roles' => ['admin'],
                'children' => [
                    ['label' => 'Exams', 'route' => 'academics.exams.index'],
                    ['label' => 'Exam schedules', 'route' => 'javascript:void(0)'],
                    ['label' => 'Exam results', 'route' => 'academics.results.index'],
                    ['label' => 'Publish results', 'route' => 'academics.results.publish'],
                    ['label' => 'Transcripts', 'route' => 'academics.transcripts.index'],
                ],
            ],
            [
                'label' => 'Subjects',
                'route' => 'academics.subjects.index',
                'icon' => 'ri-book-2-line',
                'roles' => ['teacher'],
            ],
            [
                'label' => 'Syllabi',
                'route' => 'academics.syllabi.index',
                'icon' => 'ri-book-open-line',
                'roles' => ['teacher'],
            ],
        ];
    }
}
