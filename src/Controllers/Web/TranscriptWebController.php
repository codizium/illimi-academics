<?php

namespace Illimi\Academics\Controllers\Web;

use Illimi\Academics\Models\Transcript;
use Illimi\Students\Models\Student;

class TranscriptWebController extends AcademicsWebController
{
    public function index()
    {
        $students = $this->queryFor(Student::class)->orderBy('first_name')->orderBy('last_name')->get();
        $transcripts = $this->queryFor(Transcript::class)
            ->with('student')
            ->latest('generated_at')
            ->paginate(20);

        return \Inertia\Inertia::render('Academics/Transcripts', compact('students', 'transcripts'));
    }
}
