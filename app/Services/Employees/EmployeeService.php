<?php

namespace App\Services\Employees;

use App\Data\Employee\EmployeeUpsertData;
use App\Models\Employee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EmployeeService
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Employee::query()->paginate($perPage);
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
