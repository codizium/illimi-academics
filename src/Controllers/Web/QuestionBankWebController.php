<?php

namespace Illimi\Academics\Controllers\Web;

use Illimi\Academics\Models\AcademicClass;
use Illimi\Academics\Models\QuestionBank;
use Illimi\Academics\Models\Subject;

class QuestionBankWebController extends AcademicsWebController
{
    public function index()
    {
        $questionBanks = $this->queryFor(QuestionBank::class)
            ->with(['subject', 'academicClass'])
            ->withCount('questions')
            ->latest()
            ->get();
        $subjects = $this->queryFor(Subject::class)
            ->with(['classes' => fn ($query) => $query->orderBy('name')])
            ->orderBy('name')
            ->get();
        $classes = $this->queryFor(AcademicClass::class)->orderBy('name')->get();

        return view('illimi-academics::pages.question-banks', compact('questionBanks', 'subjects', 'classes'));
    }

    public function create()
    {
        $subjects = $this->queryFor(Subject::class)
            ->with(['classes' => fn ($query) => $query->orderBy('name')])
            ->orderBy('name')
            ->get();
        $classes = $this->queryFor(AcademicClass::class)->orderBy('name')->get();

        return view('illimi-academics::pages.question-bank-add', compact('subjects', 'classes'));
    }
}
