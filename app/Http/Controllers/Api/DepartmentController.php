<?php

namespace App\Http\Controllers\Api;

use App\Data\Department\DepartmentUpsertData;
use App\Http\Controllers\Controller;
use App\Http\Resources\Department\DepartmentResource;
use App\Models\Department;
use App\Services\DepartmentService;
use App\Traits\RespondsWithJson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DepartmentController extends Controller
{
    use RespondsWithJson;

    public function __construct(private DepartmentService $departments) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) ($request->query('per_page', 15));
        $paginator = $this->departments->paginate($perPage);

        return $this->returnPaginatedData(
            item: $paginator,
            message: 'Departments retrieved',
            resourcePath: DepartmentResource::class
        );
    }

    public function store(Request $request): JsonResponse
    {
        $dto = DepartmentUpsertData::from($request->all());
        $department = $this->departments->create($dto);

        return $this->returnItemWithSuccessMessage(
            item: new DepartmentResource($department),
            message: 'Department created'
        );
    }

    public function show(Department $department): JsonResponse
    {
        return $this->returnItemWithSuccessMessage(
            item: new DepartmentResource($department),
            message: 'Department retrieved'
        );
    }

    public function update(Request $request, Department $department): JsonResponse
    {
        $dto = DepartmentUpsertData::from($request->all());
        $department = $this->departments->update($department, $dto);

        return $this->returnItemWithSuccessMessage(
            item: new DepartmentResource($department),
            message: 'Department updated'
        );
    }

    public function destroy(Department $department): JsonResponse
    {
        $this->departments->delete($department);

        return $this->returnSuccessMessage(message: 'Department deleted', code: Response::HTTP_NO_CONTENT);
    }
}
