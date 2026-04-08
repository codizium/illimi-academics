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
            ->get();

        return view('illimi-academics::pages.transcripts', compact('students', 'transcripts'));
    }
}
