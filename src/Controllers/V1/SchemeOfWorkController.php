<?php

namespace Illimi\Academics\Controllers\V1;

use Codizium\Core\Controllers\BaseController;
use Codizium\Core\Helpers\CoreJsonResponse;
use Illimi\Academics\Requests\StoreSchemeOfWorkRequest;
use Illimi\Academics\Requests\UpdateSchemeOfWorkRequest;
use Illimi\Academics\Resources\SchemeOfWorkResource;
use Illimi\Academics\Services\SchemeOfWorkService;
use Illuminate\Http\Request;

class SchemeOfWorkController extends BaseController
{
    public function __construct(
        protected SchemeOfWorkService $service,
        protected CoreJsonResponse $response
    ) {}

    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = array_filter($request->only(['syllabus_id', 'academic_year_id', 'academic_term_id', 'teacher_id']));

        $schemes = $this->service->list($filters, $perPage);

        return $this->response->success(SchemeOfWorkResource::collection($schemes), 'Schemes of Work retrieved successfully');
    }

    public function store(StoreSchemeOfWorkRequest $request)
    {
        $data = $request->validated();
        
        // If teacher_id is not provided, try to default it
        if (empty($data['teacher_id'])) {
            $user = auth()->user();
            if ($user?->hasRole('teacher')) {
                $data['teacher_id'] = \Illimi\Staff\Models\Staff::where('user_id', $user->id)->first()?->id;
            } else {
                // If admin, default to the subject's teacher if available
                $syllabus = \Illimi\Academics\Models\Syllabus::with('subject.teachers')->find($data['syllabus_id']);
                $data['teacher_id'] = $syllabus?->subject?->teachers->first()?->id;
            }
        }

        $scheme = $this->service->create($data);

        return $this->response->success(new SchemeOfWorkResource($scheme), 'Scheme of Work created successfully', 201);
    }

    public function show(string $id)
    {
        $scheme = $this->service->findById($id);

        if (! $scheme) {
            return $this->response->error('Scheme of Work not found', 404);
        }

        return $this->response->success(new SchemeOfWorkResource($scheme), 'Scheme of Work retrieved successfully');
    }

    public function update(UpdateSchemeOfWorkRequest $request, string $id)
    {
        $scheme = $this->service->update($id, $request->validated());

        if (! $scheme) {
            return $this->response->error('Scheme of Work not found', 404);
        }

        return $this->response->success(new SchemeOfWorkResource($scheme), 'Scheme of Work updated successfully');
    }

    public function destroy(string $id)
    {
        $deleted = $this->service->delete($id);

        if (! $deleted) {
            return $this->response->error('Scheme of Work not found', 404);
        }

        return $this->response->success([], 'Scheme of Work deleted successfully');
    }
}
