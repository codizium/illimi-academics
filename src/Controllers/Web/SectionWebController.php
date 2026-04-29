<?php

namespace Illimi\Academics\Controllers\Web;

use Illimi\Academics\Models\Section;
use \Inertia\Inertia;

class SectionWebController extends AcademicsWebController
{
    public function index()
    {
        $sections = $this->queryFor(Section::class)
            ->with('classes')
            ->latest()
            ->get();

        return \Inertia\Inertia::render('Academics/Sections', compact('sections'));
    }
}
