<?php

namespace Illimi\Academics\Controllers\Web;

use Illimi\Academics\Models\AcademicYear;
use Illimi\Academics\Models\AcademicTerm;
use Illuminate\Http\Request;

class AcademicTermWebController extends AcademicsWebController
{
    public function index(Request $request)
    {
        $terms = $this->queryFor(AcademicTerm::class)
        ->where([
                ...$request->only(['academic_year_id', 'organization_id', 'name', 'status']),
                // 'academic_year_id' => $request->get('year_id') 
        ])
            ->with('academicYear')
            ->orderByDesc('start_date');

        if($request->get('year_id'))
        {
            $terms->where('academic_year_id', $request->get('year_id'));
        }
        
        $terms = $terms->get();
        $academicYears = $this->queryFor(AcademicYear::class)
            ->orderByDesc('start_date')
            ->get();
        $statuses = ['active', 'inactive', 'closed'];

        return \Inertia\Inertia::render('Academics/AcademicTerms/Index', compact('terms', 'academicYears', 'statuses'));
    }

    public function create()
    {
        $academicYears = $this->queryFor(AcademicYear::class)
            ->orderByDesc('start_date')
            ->get();
        $statuses = ['active', 'inactive', 'closed'];

        return \Inertia\Inertia::render('Academics/AcademicTerms/Add', compact('academicYears', 'statuses'));
    }

    public function edit(Request $request)
    {
        $term = $this->findForEdit($request, AcademicTerm::class, ['academicYear']);
        $academicYears = $this->queryFor(AcademicYear::class)
            ->orderByDesc('start_date')
            ->get();
        $statuses = ['active', 'inactive', 'closed'];

        return \Inertia\Inertia::render('Academics/AcademicTerms/Edit', compact('term', 'academicYears', 'statuses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'academic_year_id' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'status' => 'required|string'
        ]);
        $data['organization_id'] = organization()->id;
        AcademicTerm::create($data);
        return redirect()->back()->with('success', 'Academic term created.');
    }
}
