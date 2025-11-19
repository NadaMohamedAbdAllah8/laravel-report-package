<?php

use App\Data\Department\DepartmentUpsertData;
use App\Data\PaginationData;
use App\Models\Department;
use App\Services\Departments\DepartmentService;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    $this->service = new DepartmentService;
});

test('create persists department', function (): void {
    $data = Department::factory()->make();
    $dto = DepartmentUpsertData::from($data->toArray());

    $department = $this->service->create(data: $dto);

    $this->assertDatabaseHas(Department::class, [
        'id' => $department->id,
        'name' => $dto->name,
    ]);
});

test('update updates department name', function (): void {
    $department = Department::factory()->create();

    $data = Department::factory()->make();
    $dto = DepartmentUpsertData::from([
        'name' => $data->name,
    ]);

    $this->service->update(department: $department, data: $dto);

    $this->assertDatabaseHas(Department::class, [
        'id' => $department->id,
        'name' => $dto->name,
    ]);
});

test('delete soft deletes department', function (): void {
    $department = Department::factory()->create();

    $this->service->delete($department);

    $this->assertSoftDeleted(Department::class, [
        'id' => $department->id,
    ]);
});

test('paginate after creations reflects db state', function (): void {
    Department::factory(3)->create();

    $paginationData = PaginationData::from();
    $this->service->paginate(data: $paginationData);

    $this->assertDatabaseCount(Department::class, 3);
});
