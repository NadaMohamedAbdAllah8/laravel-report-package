<?php

namespace App\Services\Departments;

use App\Data\Department\DepartmentUpsertData;
use App\Data\PaginationData;
use App\Models\Department;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DepartmentService
{
    public function paginate(PaginationData $data): LengthAwarePaginator
    {
        return Department::query()->paginate(
            perPage: $data->per_page,
            page: $data->page
        );
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
