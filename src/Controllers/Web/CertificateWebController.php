<?php

namespace Illimi\Academics\Controllers\Web;

use Illimi\Academics\Models\AcademicClass;
use Illimi\Academics\Models\AcademicYear;
use Illimi\Students\Models\Student;

class CertificateWebController extends AcademicsWebController
{
    public function index()
    {
        $students = $this->queryFor(Student::class)
            ->with('class')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name', 'admission_number', 'photo_url', 'class_id']);

        $classes = $this->queryFor(AcademicClass::class)
            ->orderBy('name')
            ->get(['id', 'name']);

        $academicYears = $this->queryFor(AcademicYear::class)
            ->orderByDesc('start_date')
            ->get(['id', 'name']);

        return \Inertia\Inertia::render('Academics/Certificates', compact('students', 'classes', 'academicYears'));
    }
}
