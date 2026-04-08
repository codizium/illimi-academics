<?php

namespace Illimi\Academics\Controllers\Web;

use Illimi\Academics\Models\AcademicYear;

class AcademicYearWebController extends AcademicsWebController
{
    public function index()
    {
        $academicYears = $this->queryFor(AcademicYear::class)
            ->withCount('terms')
            ->orderByDesc('start_date')
            ->get();
        $statuses = ['active', 'inactive', 'closed'];

        return view('illimi-academics::pages.academic-years', compact('academicYears', 'statuses'));
    }
}
