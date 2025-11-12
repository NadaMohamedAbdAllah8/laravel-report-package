<?php

namespace App\Http\Controllers\Api\Departments;

use App\Data\Department\DepartmentUpsertData;
use App\Data\PaginationData;
use App\Http\Controllers\Controller;
use App\Http\Resources\Department\DepartmentResource;
use App\Models\Department;
use App\Services\Departments\DepartmentService;
use App\Traits\RespondsWithJson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DepartmentController extends Controller
{
    use RespondsWithJson;

    public function __construct(private DepartmentService $departmentService) {}

    public function index(Request $request): JsonResponse
    {
        $dto = PaginationData::from($request->all());
        $paginator = $this->departmentService->paginate(data: $dto);

        return $this->returnPaginatedData(
            item: $paginator,
            message: 'Departments retrieved',
            resourcePath: DepartmentResource::class
        );
    }

    public function store(Request $request): JsonResponse
    {
        $dto = DepartmentUpsertData::from($request->all());
        $department = $this->departmentService->create($dto);

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
        $department = $this->departmentService->update($department, $dto);

        return $this->returnItemWithSuccessMessage(
            item: new DepartmentResource($department),
            message: 'Department updated'
        );
    }

    public function destroy(Department $department): JsonResponse
    {
        $this->departmentService->delete($department);

        return $this->returnSuccessMessage(message: 'Department deleted', code: Response::HTTP_NO_CONTENT);
    }
}
