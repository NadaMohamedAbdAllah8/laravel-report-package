<?php

namespace App\Services;

use App\Data\Department\DepartmentUpsertData;
use App\Models\Department;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DepartmentService
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Department::query()->paginate($perPage);
    }

    public function create(DepartmentUpsertData $data): Department
    {
        return Department::create($data->toFilteredArray());
    }

    public function update(Department $department, DepartmentUpsertData $data): Department
    {
        $department->fill($data->toFilteredArray());
        $department->save();

        return $department;
    }

    public function delete(Department $department): void
    {
        $department->delete();
    }
}
