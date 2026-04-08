<?php

namespace Illimi\Academics\Controllers\Web;

use Illimi\Academics\Models\GradeScale;

class GradeScaleWebController extends AcademicsWebController
{
    public function index()
    {
        $gradeScales = $this->queryFor(GradeScale::class)
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        return view('illimi-academics::pages.grade-scales', compact('gradeScales'));
    }
}
