<?php

namespace Illimi\Academics\Controllers\Web;

use Illimi\Academics\Models\LessonPlan;
use Illimi\Academics\Models\SchemeOfWork;
use Illimi\Academics\Models\Syllabus;
use Inertia\Inertia;

class LessonPlanWebController extends AcademicsWebController
{
    public function index()
    {
        $plans = $this->queryFor(LessonPlan::class)
            ->with(['schemeOfWork.syllabus.subject', 'teacher', 'attachments'])
            ->latest('date')
            ->paginate(20);

        $schemes = $this->queryFor(SchemeOfWork::class)
            ->with(['syllabus.subject.teachers', 'academicYear', 'academicTerm'])
            ->orderBy('week_number')
            ->get();

        $syllabi = $this->queryFor(Syllabus::class)
            ->with(['subject.teachers'])
            ->orderBy('title')
            ->get();

        $staff = $this->queryFor(\Illimi\Staff\Models\Staff::class)
            ->orderBy('first_name')
            ->get();

        return Inertia::render('Academics/LessonPlan', compact('plans', 'schemes', 'syllabi', 'staff'));
    }
}
