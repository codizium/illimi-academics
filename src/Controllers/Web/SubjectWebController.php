<?php

namespace Illimi\Academics\Controllers\Web;

use Illimi\Academics\Models\AcademicClass;
use Illimi\Academics\Models\Subject;
use Illimi\Staff\Models\Staff;

class SubjectWebController extends AcademicsWebController
{
    public function index()
    {
        $subjects = $this->queryFor(Subject::class)
            ->with(['teachers', 'classes', 'currentSyllabus'])
            ->orderBy('name')
            ->get();
        $teachers = $this->queryFor(Staff::class)
            ->where('status', Staff::STATUS_ACTIVE)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
        $classes = $this->queryFor(AcademicClass::class)
            ->orderBy('name')
            ->get();

        return view('illimi-academics::pages.subjects', compact('subjects', 'teachers', 'classes'));
    }
}
