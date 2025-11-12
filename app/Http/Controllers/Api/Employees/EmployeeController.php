<?php

namespace App\Http\Controllers\Api\Employees;

use App\Data\Employee\EmployeeUpsertData;
use App\Data\PaginationData;
use App\Http\Controllers\Controller;
use App\Http\Resources\Employee\EmployeeResource;
use App\Models\Employee;
use App\Services\Employees\EmployeeService;
use App\Traits\RespondsWithJson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmployeeController extends Controller
{
    use RespondsWithJson;

    public function __construct(private EmployeeService $employeeService) {}

    public function index(Request $request): JsonResponse
    {
        $dto = PaginationData::from($request->all());
        $paginator = $this->employeeService->paginate(data: $dto);

        return $this->returnPaginatedData(
            item: $paginator,
            message: 'Employees retrieved',
            resourcePath: EmployeeResource::class
        );
    }

    public function store(Request $request): JsonResponse
    {
        $dto = EmployeeUpsertData::from($request->all());
        $employee = $this->employeeService->create($dto);

        return $this->returnItemWithSuccessMessage(
            item: new EmployeeResource($employee),
            message: 'Employee created'
        );
    }

    public function show(Employee $employee): JsonResponse
    {
        return $this->returnItemWithSuccessMessage(
            item: new EmployeeResource($employee),
            message: 'Employee retrieved'
        );
    }

    public function update(Request $request, Employee $employee): JsonResponse
    {
        $dto = EmployeeUpsertData::from($request->all());
        $employee = $this->employeeService->update(employee: $employee, data: $dto);

        return $this->returnItemWithSuccessMessage(
            item: new EmployeeResource($employee),
            message: 'Employee updated'
        );
    }

    public function destroy(Employee $employee): JsonResponse
    {
        $this->employeeService->delete(employee: $employee);

        return $this->returnSuccessMessage(message: 'Employee deleted', code: Response::HTTP_NO_CONTENT);
    }
}
