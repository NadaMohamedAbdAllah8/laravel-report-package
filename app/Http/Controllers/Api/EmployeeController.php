<?php

namespace App\Http\Controllers\Api;

use App\Data\Employee\EmployeeUpsertData;
use App\Http\Controllers\Controller;
use App\Http\Resources\Employee\EmployeeResource;
use App\Models\Employee;
use App\Services\EmployeeService;
use App\Traits\RespondsWithJson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmployeeController extends Controller
{
    use RespondsWithJson;

    public function __construct(private EmployeeService $employees) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) ($request->query('per_page', 15));
        $paginator = $this->employees->paginate($perPage);

        return $this->returnPaginatedData(
            item: $paginator,
            message: 'Employees retrieved',
            resourcePath: EmployeeResource::class
        );
    }

    public function store(Request $request): JsonResponse
    {
        $dto = EmployeeUpsertData::from($request->all());
        $employee = $this->employees->create($dto);

        return $this->returnItemWithSuccessMessage(
            item: EmployeeResource::make($employee),
            message: 'Employee created'
        );
    }

    public function show(Employee $employee): JsonResponse
    {
        return $this->returnItemWithSuccessMessage(
            item: EmployeeResource::make($employee),
            message: 'Employee retrieved'
        );
    }

    public function update(Request $request, Employee $employee): JsonResponse
    {
        $dto = EmployeeUpsertData::from($request->all());
        $employee = $this->employees->update(employee: $employee, data: $dto);

        return $this->returnItemWithSuccessMessage(
            item: EmployeeResource::make($employee),
            message: 'Employee updated'
        );
    }

    public function destroy(Employee $employee): JsonResponse
    {
        $this->employees->delete(employee: $employee);

        return $this->returnSuccessMessage(message: 'Employee deleted', code: Response::HTTP_NO_CONTENT);
    }
}
