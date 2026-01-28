<?php

namespace App\Services\Departments\Dto;

use App\Models\Employee;
use Spatie\LaravelData\Data;

class DepartmentEmployeeData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $title,
        public float $salary,
    ) {}

    public static function fromEmployee(Employee $employee): self
    {
        return new self(id: $employee->id, name: $employee->name, title: $employee->title, salary: $employee->salary);
    }
}
