<?php

namespace Illimi\Academics\Controllers\Web;

use Illimi\Academics\Models\GradeAppeal;
use Illimi\Academics\Models\Result;
use Illimi\Students\Models\Student;

class AppealWebController extends AcademicsWebController
{
    public function index()
    {
        $appeals = $this->queryFor(GradeAppeal::class)
            ->with(['student', 'result.student', 'result.academicClass.section'])
            ->latest()
            ->get();
        $results = $this->queryFor(Result::class)
            ->with(['student', 'academicClass.section'])
            ->latest()
            ->get();
        $students = $this->queryFor(Student::class)->orderBy('first_name')->orderBy('last_name')->get();
        $statuses = ['submitted', 'under_review', 'resolved', 'rejected'];

        return \Inertia\Inertia::render('Academics/Appeals', compact('appeals', 'results', 'students', 'statuses'));
    }

    public function create()
    {
        $results = $this->queryFor(Result::class)
            ->with(['student', 'academicClass.section'])
            ->latest()
            ->get();
        $students = $this->queryFor(Student::class)->orderBy('first_name')->orderBy('last_name')->get();

        return \Inertia\Inertia::render('Academics/AppealAdd', compact('results', 'students'));
    }
}
