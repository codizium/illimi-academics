<?php

namespace Illimi\Academics\Controllers\Web;

use Illimi\Academics\Models\Classroom;

use Inertia\Inertia;

class ClassroomWebController extends AcademicsWebController
{
    public function index()
    {
        $classrooms = $this->queryFor(Classroom::class)
            ->withCount('academicClasses')
            ->orderBy('name')
            ->get();

        return Inertia::render('Academics/Classrooms', compact('classrooms'));
    }
}
