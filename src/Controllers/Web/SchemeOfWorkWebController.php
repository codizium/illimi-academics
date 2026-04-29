<?php

namespace Illimi\Academics\Controllers\Web;

use Illimi\Academics\Models\AcademicTerm;
use Illimi\Academics\Models\AcademicYear;
use Illimi\Academics\Models\SchemeOfWork;
use Illimi\Academics\Models\Syllabus;
use Illimi\Academics\Models\AcademicClass;
use Inertia\Inertia;

class SchemeOfWorkWebController extends AcademicsWebController
{
    public function index()
    {
        $schemes = $this->queryFor(SchemeOfWork::class)
            ->with(['syllabus.subject', 'academicYear', 'academicTerm', 'teacher', 'classroom', 'attachments'])
            ->withCount('lessonPlans')
            ->orderBy('week_number')
            ->paginate(20);

        $syllabi = $this->queryFor(Syllabus::class)
            ->with(['subject.teachers'])
            ->orderBy('title')
            ->get();

        $academicYears = $this->queryFor(AcademicYear::class)
            ->orderByDesc('start_date')
            ->get();

        $academicTerms = $this->queryFor(AcademicTerm::class)
            ->orderBy('name')
            ->get();

        $classes = $this->queryFor(AcademicClass::class)
            ->orderBy('name')
            ->get();

        $staff = $this->queryFor(\Illimi\Staff\Models\Staff::class)
            ->orderBy('first_name')
            ->get();

        return Inertia::render('Academics/SchemeOfWork', compact(
            'schemes', 'syllabi', 'academicYears', 'academicTerms', 'classes', 'staff'
        ));
    }
}
