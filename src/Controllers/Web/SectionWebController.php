<?php

namespace Illimi\Academics\Controllers\Web;

use Illimi\Academics\Models\Section;

class SectionWebController extends AcademicsWebController
{
    public function index()
    {
        $sections = $this->queryFor(Section::class)
            ->with('classes')
            ->latest()
            ->get();

        return view('illimi-academics::pages.sections', compact('sections'));
    }
}
