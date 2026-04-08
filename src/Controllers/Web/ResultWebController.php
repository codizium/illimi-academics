<?php

namespace Illimi\Academics\Controllers\Web;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illimi\Academics\Models\AcademicTerm;
use Illimi\Academics\Models\AcademicYear;
use Illimi\Academics\Models\AcademicClass;
use Illimi\Academics\Models\GradeScale;
use Illimi\Academics\Models\Result;
use Illimi\Academics\Services\ResultService;
use Illuminate\Http\Request;

class ResultWebController extends AcademicsWebController
{
    public function __construct(
        protected ResultService $resultService
    ) {
    }

    public function index()
    {
        $results = $this->queryFor(Result::class)
            ->with(['student', 'academicClass'])
            ->latest()
            ->get();

        return view('illimi-academics::pages.results', compact('results'));
    }

    public function publish(Request $request)
    {
        $classes = $this->queryFor(AcademicClass::class)->orderBy('name')->get();
        $academicYears = $this->queryFor(AcademicYear::class)->orderByDesc('start_date')->get();

        $selectedAcademicYearId = $request->query('academic_year_id')
            ?: $academicYears->firstWhere('status', 'active')?->id
            ?: $academicYears->first()?->id;

        $academicTerms = $this->queryFor(AcademicTerm::class)
            ->when($selectedAcademicYearId, fn ($query) => $query->where('academic_year_id', $selectedAcademicYearId))
            ->orderBy('start_date')
            ->get();

        $selectedAcademicTermId = $request->query('academic_term_id')
            ?: $academicTerms->firstWhere('status', 'active')?->id
            ?: $academicTerms->first()?->id;

        $classSummaries = $this->resultService->publicationScopes(
            $this->organizationId(),
            $selectedAcademicYearId,
            $selectedAcademicTermId
        );

        return view('illimi-academics::pages.results-publish', compact(
            'classes',
            'academicYears',
            'academicTerms',
            'selectedAcademicYearId',
            'selectedAcademicTermId',
            'classSummaries'
        ));
    }

    public function manage(Request $request)
    {
        $request->validate([
            'class_id' => ['required', 'uuid'],
            'academic_year_id' => ['required', 'uuid'],
            'academic_term_id' => ['required', 'uuid'],
        ]);

        $publicationData = $this->resultService->publicationPreview(
            $request->query('class_id'),
            $request->query('academic_year_id'),
            $request->query('academic_term_id'),
            $this->organizationId()
        );

        return view('illimi-academics::pages.results-publish-manage', $publicationData + [
            'classId' => $request->query('class_id'),
            'academicYearId' => $request->query('academic_year_id'),
            'academicTermId' => $request->query('academic_term_id'),
        ]);
    }

    public function check(Request $request)
    {
        return view('illimi-academics::frontend.result-check', [
            'admissionNumber' => trim((string) $request->query('admission_number', '')),
            'token' => strtoupper(trim((string) $request->query('token', ''))),
            'organization' => function_exists('organization') ? organization() : null,
        ]);
    }

    public function showCheck(Request $request)
    {
        $admissionNumber = trim((string) $request->query('admission_number', ''));
        $token = strtoupper(trim((string) $request->query('token', '')));
        $resultSlip = null;
        $qrSvg = null;
        $gradeScales = $this->queryFor(GradeScale::class)
            ->orderByDesc('max_score')
            ->orderByDesc('min_score')
            ->get(['id', 'name', 'code', 'description', 'min_score', 'max_score']);

        if ($token !== '') {
            $resultSlip = $this->resultService->publicResultSlip($admissionNumber, $token, $this->organizationId());
        }

        if ($resultSlip) {
            $verificationText = implode(' | ', array_filter([
                $resultSlip['student']['admission_number'] ?? null,
                $resultSlip['student']['full_name'] ?? null,
                $resultSlip['academic_year']['name'] ?? null,
                $resultSlip['academic_term']['name'] ?? null,
                $resultSlip['published_result']['token'] ?? null,
            ]));

            $renderer = new ImageRenderer(
                new RendererStyle(120),
                new SvgImageBackEnd()
            );

            $qrSvg = (new Writer($renderer))->writeString($verificationText);
        }

        return view('illimi-academics::frontend.result-display', [
            'admissionNumber' => $admissionNumber,
            'token' => $token,
            'resultSlip' => $resultSlip,
            'organization' => function_exists('organization') ? organization() : null,
            'qrSvg' => $qrSvg,
            'gradeScales' => $gradeScales,
        ]);
    }
}
