<?php

namespace Illimi\Academics\Controllers\V1;

use Codizium\Core\Controllers\BaseController;
use Codizium\Core\Helpers\CoreJsonResponse;
use Illimi\Academics\Requests\StoreLessonPlanRequest;
use Illimi\Academics\Requests\UpdateLessonPlanRequest;
use Illimi\Academics\Resources\LessonPlanResource;
use Illimi\Academics\Services\LessonPlanService;
use Illuminate\Http\Request;

class LessonPlanController extends BaseController
{
    public function __construct(
        protected LessonPlanService $service,
        protected CoreJsonResponse $response
    ) {}

    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = array_filter($request->only(['scheme_of_work_id', 'teacher_id', 'status']));

        $plans = $this->service->list($filters, $perPage);

        return $this->response->success(LessonPlanResource::collection($plans), 'Lesson Plans retrieved successfully');
    }

    public function store(StoreLessonPlanRequest $request)
    {
        $data = $request->validated();

        // If teacher_id is not provided, try to default it
        if (empty($data['teacher_id'])) {
            $user = auth()->user();
            if ($user?->hasRole('teacher')) {
                $data['teacher_id'] = \Illimi\Staff\Models\Staff::where('user_id', $user->id)->first()?->id;
            } else {
                // If admin, default to the scheme of work's teacher if available
                $scheme = \Illimi\Academics\Models\SchemeOfWork::find($data['scheme_of_work_id']);
                $data['teacher_id'] = $scheme?->teacher_id;
            }
        }

        $plan = $this->service->create($data);

        return $this->response->success(new LessonPlanResource($plan), 'Lesson Plan created successfully', 201);
    }

    public function show(string $id)
    {
        $plan = $this->service->findById($id);

        if (! $plan) {
            return $this->response->error('Lesson Plan not found', 404);
        }

        return $this->response->success(new LessonPlanResource($plan), 'Lesson Plan retrieved successfully');
    }

    public function update(UpdateLessonPlanRequest $request, string $id)
    {
        $plan = $this->service->update($id, $request->validated());

        if (! $plan) {
            return $this->response->error('Lesson Plan not found', 404);
        }

        return $this->response->success(new LessonPlanResource($plan), 'Lesson Plan updated successfully');
    }

    public function destroy(string $id)
    {
        $deleted = $this->service->delete($id);

        if (! $deleted) {
            return $this->response->error('Lesson Plan not found', 404);
        }

        return $this->response->success([], 'Lesson Plan deleted successfully');
    }
}
