<?php

namespace Illimi\Academics\Controllers\Web;

use Illimi\Academics\Models\AcademicYear;
use Illimi\Academics\Models\AcademicTerm;
use Illuminate\Http\Request;

class AcademicTermWebController extends AcademicsWebController
{
    public function index()
    {
        $terms = $this->queryFor(AcademicTerm::class)
            ->with('academicYear')
            ->orderByDesc('start_date')
            ->get();
        $academicYears = $this->queryFor(AcademicYear::class)
            ->orderByDesc('start_date')
            ->get();
        $statuses = ['active', 'inactive', 'closed'];

        return view('illimi-academics::pages.academic-terms', compact('terms', 'academicYears', 'statuses'));
    }

    public function create()
    {
        $academicYears = $this->queryFor(AcademicYear::class)
            ->orderByDesc('start_date')
            ->get();
        $statuses = ['active', 'inactive', 'closed'];

        return view('illimi-academics::pages.academic-term-add', compact('academicYears', 'statuses'));
    }

    public function edit(Request $request)
    {
        $term = $this->findForEdit($request, AcademicTerm::class, ['academicYear']);
        $academicYears = $this->queryFor(AcademicYear::class)
            ->orderByDesc('start_date')
            ->get();
        $statuses = ['active', 'inactive', 'closed'];

        return view('illimi-academics::pages.academic-term-edit', compact('term', 'academicYears', 'statuses'));
    }
}
