<?php

namespace Illimi\Academics\Controllers\Web;

use Illimi\Academics\Models\Subject;
use Illimi\Academics\Models\Syllabus;

class SyllabusWebController extends AcademicsWebController
{
    public function index()
    {
        $syllabi = $this->queryFor(Syllabus::class)
            ->with(['subject', 'attachments'])
            ->withCount('attachments')
            ->latest()
            ->get();

        $subjects = $this->queryFor(Subject::class)
            ->orderBy('name')
            ->get();

        return view('illimi-academics::pages.syllabi', compact('syllabi', 'subjects'));
    }
}
