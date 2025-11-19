<?php

use App\Data\Employee\EmployeeUpsertData;
use App\Data\PaginationData;
use App\Models\Department;
use App\Models\Employee;
use App\Services\Employees\EmployeeService;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    $this->service = new EmployeeService;
});

test('create persists employee', function (): void {
    $department = Department::factory()->create();
    $data = Employee::factory()->make(['department_id' => $department->id]);
    $dto = EmployeeUpsertData::from($data->toArray());

    $employee = $this->service->create(data: $dto);

    $this->assertDatabaseHas(Employee::class, [
        'id' => $employee->id,
        'name' => $dto->name,
        'email' => $dto->email,
        'department_id' => $department->id,
        'salary' => $dto->salary,
        'title' => $dto->title,
    ]);
});

test('update updates only provided fields', function (): void {
    $employee = Employee::factory()->create();
    $initialTitle = $employee->title;

    $data = Employee::factory()->make();
    $dto = EmployeeUpsertData::from([
        'name' => $data->name,
        'email' => $data->email,
    ]);

    $this->service->update(employee: $employee, data: $dto);

    $this->assertDatabaseHas(Employee::class, [
        'id' => $employee->id,
        'name' => $dto->name,
        'email' => $dto->email,
        'title' => $initialTitle,
    ]);
});

test('delete soft deletes employee', function (): void {
    $employee = Employee::factory()->create();

    $this->service->delete($employee);

    $this->assertSoftDeleted(Employee::class, [
        'id' => $employee->id,
    ]);
});

test('paginate after creations reflects db state', function (): void {
    Employee::factory(3)->create();

    $paginationData = PaginationData::from();
    $this->service->paginate(data: $paginationData);

    $this->assertDatabaseCount(Employee::class, 3);
});
