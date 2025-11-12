<?php

namespace App\Services\Employees;

use App\Data\Employee\EmployeeUpsertData;
use App\Data\PaginationData;
use App\Models\Employee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EmployeeService
{
    public function paginate(PaginationData $data): LengthAwarePaginator
    {
        return Employee::query()->paginate(
            perPage: $data->per_page,
            page: $data->page
        );
    }

    public function create(EmployeeUpsertData $data): Employee
    {
        $payload = $data->toFilteredArray();

        return Employee::create($payload);
    }

    public function update(Employee $employee, EmployeeUpsertData $data): Employee
    {
        $payload = $data->toFilteredArray();
        $employee->fill($payload);
        $employee->save();

        return $employee;
    }

    public function delete(Employee $employee): void
    {
        $employee->delete();
    }
}
