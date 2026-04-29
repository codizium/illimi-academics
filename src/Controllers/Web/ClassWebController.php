<?php

namespace Illimi\Academics\Controllers\Web;

use Illimi\Academics\Models\AcademicClass;
use Illimi\Academics\Models\Classroom;
use Illimi\Academics\Models\Section;
use Illimi\Staff\Models\Staff;
use Illuminate\Http\Request;

class ClassWebController extends AcademicsWebController
{
    public function index(Request $request)
    {
        $classes = $this->queryFor(AcademicClass::class)
            ->with('classTeacher', 'section', 'classroom')
            ->withCount('students')
            ->where($request->only(['id', 'name', 'section_id', 'classroom_id', 'class_teacher_id']))
            ->latest()
            ->orderBy('level', 'asc')
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
            

        return \Inertia\Inertia::render('Academics/Classes', [
            'classes' => $classes,
            'teachers' => $teachers,
            'sections' => $sections,
            'classrooms' => $classrooms
        ]);
    }
}
