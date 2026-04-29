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
            ->paginate(20);

        $subjects = $this->queryFor(Subject::class)
            ->orderBy('name')
            ->get();

        return \Inertia\Inertia::render('Academics/Syllabi', compact('syllabi', 'subjects'));
    }
}
