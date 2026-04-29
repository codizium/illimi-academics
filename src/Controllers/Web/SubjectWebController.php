<?php

namespace Illimi\Academics\Controllers\Web;

use Illimi\Academics\Models\AcademicClass;
use Illimi\Academics\Models\Subject;
use Illimi\Staff\Models\Staff;
use Illuminate\Http\Request;

class SubjectWebController extends AcademicsWebController
{
    public function index(Request $request)
    {
        $subjects = $this->queryFor(Subject::class)
            ->with(['teachers', 'classes', 'currentSyllabus'])
            ->orderBy('name')
            ->where($request->only([
                'organization_id',
                'name',
                'code',
                'description',
                'is_compulsory',
                'credit_units',
            ]))
            ->get();
        
        $teachers = $this->queryFor(Staff::class)
            ->where('status', Staff::STATUS_ACTIVE)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
            
        $classes = $this->queryFor(AcademicClass::class)
            ->orderBy('name')
            ->get();

        return \Inertia\Inertia::render('Academics/Subjects', compact('subjects', 'teachers', 'classes'));
    }
}
