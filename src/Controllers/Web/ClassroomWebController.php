<?php

namespace Illimi\Academics\Controllers\Web;

use Illimi\Academics\Models\Classroom;

class ClassroomWebController extends AcademicsWebController
{
    public function index()
    {
        $classrooms = $this->queryFor(Classroom::class)
            ->withCount('academicClasses')
            ->orderBy('name')
            ->get();

        return view('illimi-academics::pages.classrooms', compact('classrooms'));
    }
}
