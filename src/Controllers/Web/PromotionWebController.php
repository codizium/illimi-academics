<?php

namespace Illimi\Academics\Controllers\Web;

use Illimi\Academics\Models\AcademicClass;
use Illimi\Academics\Models\AcademicYear;
use Illimi\Students\Models\Student;
use Illuminate\Http\Request;

class PromotionWebController extends AcademicsWebController
{
    public function index(Request $request)
    {
        $classes = $this->queryFor(AcademicClass::class)
            ->orderBy('name')
            ->get(['id', 'name']);

        $academicYears = $this->queryFor(AcademicYear::class)
            ->orderByDesc('start_date')
            ->get(['id', 'name', 'status']);

        $students = [];
        if ($request->filled('class_id')) {
            $students = $this->queryFor(Student::class)
                ->with('class')
                ->where('academic_class_id', $request->class_id)
                ->orderBy('first_name')
                ->get();
        }

        return \Inertia\Inertia::render('Academics/Promotion', compact('classes', 'academicYears', 'students'));
    }
}
