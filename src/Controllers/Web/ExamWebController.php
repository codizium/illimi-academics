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
            ->with(['subject', 'academicClass'])
            ->latest()
            ->get();
        $subjects = $this->queryFor(Subject::class)
            ->with(['classes' => fn ($query) => $query->orderBy('name')])
            ->orderBy('name')
            ->get();
        $classes = $this->queryFor(AcademicClass::class)->orderBy('name')->get();

        return view('illimi-academics::pages.exams', compact('exams', 'subjects', 'classes'));
    }

    public function create()
    {
        $subjects = $this->queryFor(Subject::class)
            ->with(['classes' => fn ($query) => $query->orderBy('name')])
            ->orderBy('name')
            ->get();
        $classes = $this->queryFor(AcademicClass::class)->orderBy('name')->get();

        return view('illimi-academics::pages.exam-add', compact('subjects', 'classes'));
    }

    public function attempts()
    {
        $attempts = $this->queryFor(ExamAttempt::class)
            ->with(['exam', 'student'])
            ->latest()
            ->get();

        return view('illimi-academics::pages.exam-attempts', compact('attempts'));
    }

    public function itemAnalysis()
    {
        $exams = $this->queryFor(Exam::class)->orderByDesc('starts_at')->get();

        return view('illimi-academics::pages.item-analysis', compact('exams'));
    }
}
