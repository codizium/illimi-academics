<?php

namespace Illimi\Academics\Controllers\Web;

use Illimi\Academics\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearWebController extends AcademicsWebController
{
    public function index(Request $request)
    {
        $academicYears = $this->queryFor(AcademicYear::class)
        ->where($request->only([
            'organization_id',
            'name',
            'status'
        ]))
            ->withCount('terms')
            ->orderByDesc('start_date')
            ->get();
        $statuses = ['active', 'inactive', 'closed'];

        return \Inertia\Inertia::render('Academics/AcademicYears', compact('academicYears', 'statuses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'status' => 'required|string'
        ]);
        $data['organization_id'] = organization()->id;
        AcademicYear::create($data);
        return redirect()->back()->with('success', 'Academic year created.');
    }
}
