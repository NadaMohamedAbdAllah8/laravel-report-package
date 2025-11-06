<?php

namespace App\Http\Resources\Employee;

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
            'department_id' => $this->department_id,
            'manager_id' => $this->manager_id,
            'salary' => $this->salary,
            'title' => $this->title,
        ];
    }
}
