<?php

namespace Illimi\Academics\Controllers\Web;

use Illimi\Academics\Models\AcademicClass;
use Illimi\Academics\Models\Exam;
use Illimi\Academics\Models\ExamAttempt;
use Illimi\Academics\Models\Subject;

class ExamWebController extends AcademicsWebController
{
    public function index()
    {
        $exams = $this->queryFor(Exam::class)
            ->with(['subject', 'academicClass.section'])
            ->latest()
            ->paginate(15);
        $subjects = $this->queryFor(Subject::class)
            ->with(['classes' => fn ($query) => $query->orderBy('name')])
            ->orderBy('name')
            ->get();
        $classes = $this->queryFor(AcademicClass::class)->with('section')->orderBy('name')->get();

        return \Inertia\Inertia::render('Academics/Exams', compact('exams', 'subjects', 'classes'));
    }

    public function create()
    {
        $subjects = $this->queryFor(Subject::class)
            ->with(['classes' => fn ($query) => $query->orderBy('name')])
            ->orderBy('name')
            ->get();
        $classes = $this->queryFor(AcademicClass::class)->with('section')->orderBy('name')->get();

        return \Inertia\Inertia::render('Academics/ExamAdd', compact('subjects', 'classes'));
    }

    public function attempts()
    {
        $attempts = $this->queryFor(ExamAttempt::class)
            ->with(['exam', 'student'])
            ->latest()
            ->get();

        return \Inertia\Inertia::render('Academics/ExamAttempts', compact('attempts'));
    }

    public function itemAnalysis()
    {
        $exams = $this->queryFor(Exam::class)->orderByDesc('starts_at')->get();

        return \Inertia\Inertia::render('Academics/ItemAnalysis', compact('exams'));
    }
}
