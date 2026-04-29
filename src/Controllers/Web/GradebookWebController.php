<?php

namespace Illimi\Academics\Controllers\Web;

use Illimi\Academics\Enums\GradeComponentEnum;
use Illimi\Academics\Models\AcademicClass;
use Illimi\Academics\Models\GradebookEntry;
use Illimi\Academics\Models\Subject;
use Illimi\Students\Models\Student;

class GradebookWebController extends AcademicsWebController
{
    public function index()
    {
        $entries = $this->queryFor(GradebookEntry::class)
            ->with(['student', 'subject', 'academicClass'])
            ->latest()
            ->get();
        $students = $this->queryFor(Student::class)->orderBy('first_name')->orderBy('last_name')->get();
        $subjects = $this->queryFor(Subject::class)->orderBy('name')->get();
        $classes = $this->queryFor(AcademicClass::class)->orderBy('name')->get();
        $components = GradeComponentEnum::cases();

        return \Inertia\Inertia::render('Academics/Gradebook/Index', compact('entries', 'students', 'subjects', 'classes', 'components'));
    }

    public function create()
    {
        $students = $this->queryFor(Student::class)->orderBy('first_name')->orderBy('last_name')->get();
        $subjects = $this->queryFor(Subject::class)->orderBy('name')->get();
        $classes = $this->queryFor(AcademicClass::class)->orderBy('name')->get();
        $components = GradeComponentEnum::cases();

        return \Inertia\Inertia::render('Academics/Gradebook/Add', compact('students', 'subjects', 'classes', 'components'));
    }
}
