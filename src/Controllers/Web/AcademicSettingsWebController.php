<?php

namespace Illimi\Academics\Controllers\Web;

use Illimi\Academics\Models\AcademicYear;
use Illimi\Academics\Models\AcademicTerm;
use Illimi\Academics\Models\GradeScale;
use Illimi\Gradebook\Models\AssessmentTemplate;
use Illuminate\Http\Request;

class AcademicSettingsWebController extends AcademicsWebController
{
    public function index()
    {
        // Fetch Academic Years
        $academicYears = $this->queryFor(AcademicYear::class)
            ->withCount('terms')
            ->orderByDesc('start_date')
            ->get();

        // Fetch Academic Terms
        $terms = $this->queryFor(AcademicTerm::class)
            ->with('academicYear')
            ->orderByDesc('start_date')
            ->get();

        // Fetch Grade Scales
        $gradeScales = $this->queryFor(GradeScale::class)
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        // Fetch Grading Templates (from Gradebook)
        $templates = AssessmentTemplate::query()
            ->when(organization(), fn($query, $org) => $query->where('organization_id', $org->id))
            ->with(['subject', 'academicClass.section', 'academicYear', 'academicTerm', 'items'])
            ->latest()
            ->get();

        $statuses = ['active', 'inactive', 'closed'];

        $subjects = \Illimi\Academics\Models\Subject::orderBy('name')->get();
        $classes = \Illimi\Academics\Models\AcademicClass::with('section')->orderBy('name')->get();

        return view('illimi-academics::pages.academic-settings', compact(
            'academicYears',
            'terms',
            'gradeScales',
            'templates',
            'statuses',
            'subjects',
            'classes'
        ));
    }
}
