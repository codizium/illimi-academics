<?php
namespace Illimi\Academics\Middleware;

use Closure;
use Codizium\Core\Models\Organization;
use Illimi\Academics\Models\AcademicYear;
use Illimi\Academics\Models\AcademicTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;


class AcademicYearMiddleware
{
    /**
     * Only enforce an active academic year on routes that truly depend on it.
     * Pages like `/home`, academic year setup, or term setup should remain accessible.
     */
    protected array $scopedRoutePatterns = [
        'academics.gradebook.*',
        'academics.results.*',
        'academics.exams.*',
        'academics.appeals.*',
        'academics.transcripts.*',
        'v1.academics.gradebook.*',
        'v1.academics.results.*',
        'v1.academics.exams.*',
        'v1.academics.exam_attempts.*',
        'v1.academics.appeals.*',
        'v1.academics.transcripts.*',
    ];

    public function __construct(private Organization $organization)
    {

    }
    public function handle(Request $request, Closure $next)
    {
        // if (!$request->routeIs($this->scopedRoutePatterns)) {
        //     return $next($request);
        // }

        // dd($request->route()->getName(), $this->scopedRoutePatterns);

        $orgID = $this->organization?->id ?? null;
        if (!$orgID) {
            abort(401, 'Unauthorized request');
        }

        if (!user())
            return $next($request);

        $AcadYear = Session::get('academic_year');

        if (!$AcadYear) {
            $AcadYear = AcademicYear::where(['organization_id' => $orgID, 'status' => 'active'])->first();
        }



        if (!$AcadYear) {
            // abort(404, 'No active academic year found');
            
        } else if ($AcadYear) {
            app()->instance(AcademicYear::class, $AcadYear);
            $request->merge(['academic_year_id' => $AcadYear->id]);

            $term = Session::get('academic_term');

            if (!$term) {
                $term = AcademicTerm::where([
                    'status' => 'active',
                ])->first();
            }

            if ($term) {
                app()->instance(AcademicTerm::class, $term);
                $request->merge(['academic_term_id' => $term->id]);
            }
            

            
            View::share('academic_year', $AcadYear);
            View::share('academic_term', $term);
        }
        
        $terms = AcademicTerm::all();
    View::share('years', AcademicYear::all());
            View::share('terms', $terms);

        return $next($request);
    }
}
