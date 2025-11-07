<?php

namespace App\Http\Resources\Employee;

use App\Http\Resources\Department\DepartmentResource;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'department' => new DepartmentResource($this->department),
            'manager' => $this->manager ? new EmployeeSummaryResource($this->manager) : null,
            'salary' => $this->salary,
            'title' => $this->title,
        ];
    }
}
