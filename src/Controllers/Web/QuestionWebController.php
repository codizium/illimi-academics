<?php

namespace Illimi\Academics\Controllers\Web;

use Illimi\Academics\Enums\QuestionTypeEnum;
use Illimi\Academics\Models\Question;
use Illimi\Academics\Models\QuestionBank;
use Illimi\Academics\Models\Subject;

class QuestionWebController extends AcademicsWebController
{
    public function index()
    {
        $questions = $this->queryFor(Question::class)
            ->with(['questionBank', 'subject'])
            ->latest()
            ->get();
        $questionBanks = $this->queryFor(QuestionBank::class)
            ->with('subject')
            ->orderBy('name')
            ->get();
        $subjects = $this->queryFor(Subject::class)->orderBy('name')->get();
        $questionTypes = QuestionTypeEnum::cases();
        $difficulties = ['easy', 'medium', 'hard'];

        return view('illimi-academics::pages.questions', compact('questions', 'questionBanks', 'subjects', 'questionTypes', 'difficulties'));
    }

    public function create()
    {
        $questionBanks = $this->queryFor(QuestionBank::class)
            ->with('subject')
            ->orderBy('name')
            ->get();
        $subjects = $this->queryFor(Subject::class)->orderBy('name')->get();
        $questionTypes = QuestionTypeEnum::cases();
        $difficulties = ['easy', 'medium', 'hard'];

        return view('illimi-academics::pages.question-add', compact('questionBanks', 'subjects', 'questionTypes', 'difficulties'));
    }
}
