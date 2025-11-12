<?php

namespace Tests\Unit\Employees;

use App\Data\Employee\EmployeeUpsertData;
use App\Data\PaginationData;
use App\Models\Department;
use App\Models\Employee;
use App\Services\Employees\EmployeeService;
use Tests\TestCase;

class EmployeeServiceTest extends TestCase
{
    private EmployeeService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new EmployeeService;
    }

    public function test_create_persists_employee(): void
    {
        // arrange
        $department = Department::factory()->create();
        $data = Employee::factory()->make(['department_id' => $department->id]);
        $dto = EmployeeUpsertData::from($data->toArray());

        // act
        $employee = $this->service->create(data: $dto);

        // assert
        $this->assertDatabaseHas(Employee::class, [
            'id' => $employee->id,
            'name' => $dto->name,
            'email' => $dto->email,
            'department_id' => $department->id,
            'salary' => $dto->salary,
            'title' => $dto->title,
        ]);
    }

    public function test_update_updates_only_provided_fields(): void
    {
        // arrange
        $employee = Employee::factory()->create();
        $initialTitle = $employee->title;

        $data = Employee::factory()->make();
        $dto = EmployeeUpsertData::from([
            'name' => $data->name,
            'email' => $data->email,
        ]);

        // act
        $this->service->update(employee: $employee, data: $dto);

        // assert
        $this->assertDatabaseHas(Employee::class, [
            'id' => $employee->id,
            'name' => $dto->name,
            'email' => $dto->email,
            'title' => $initialTitle,
        ]);
    }

    public function test_delete_soft_deletes_employee(): void
    {
        // arrange
        $employee = Employee::factory()->create();

        // act
        $this->service->delete($employee);

        // assert
        $this->assertSoftDeleted(Employee::class, [
            'id' => $employee->id,
        ]);
    }

    public function test_paginate_after_creations_reflects_db_state(): void
    {
        // arrange
        Employee::factory(3)->create();

        // act
        $paginationData = PaginationData::from();
        $this->service->paginate(data: $paginationData);

        // assert
        $this->assertDatabaseCount(Employee::class, 3);
    }
}
