<?php

namespace Illimi\Academics\Controllers\Web;

use Illimi\Academics\Models\AcademicClass;
use Illimi\Academics\Models\Classroom;
use Illimi\Academics\Models\Section;
use Illimi\Staff\Models\Staff;

class ClassWebController extends AcademicsWebController
{
    public function index()
    {
        $classes = $this->queryFor(AcademicClass::class)
            ->with('classTeacher', 'section', 'classroom')
            ->latest()
            ->get();
        $teachers = $this->queryFor(Staff::class)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
        $sections = $this->queryFor(Section::class)
            ->orderBy('name')
            ->get();
        $classrooms = $this->queryFor(Classroom::class)
            ->orderBy('name')
            ->get();

        return view('illimi-academics::pages.classes', compact('classes', 'teachers', 'sections', 'classrooms'));
    }
}
