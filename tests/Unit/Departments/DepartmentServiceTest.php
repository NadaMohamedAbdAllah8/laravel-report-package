<?php

namespace Tests\Unit\Departments;

use App\Data\Department\DepartmentUpsertData;
use App\Data\PaginationData;
use App\Models\Department;
use App\Services\Departments\DepartmentService;
use Tests\TestCase;

class DepartmentServiceTest extends TestCase
{
    private DepartmentService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new DepartmentService;
    }

    public function test_create_persists_department(): void
    {
        // arrange
        $data = Department::factory()->make();
        $dto = DepartmentUpsertData::from($data->toArray());

        // act
        $department = $this->service->create(data: $dto);

        // assert
        $this->assertDatabaseHas(Department::class, [
            'id' => $department->id,
            'name' => $dto->name,
        ]);
    }

    public function test_update_updates_department_name(): void
    {
        // arrange
        $department = Department::factory()->create();

        $data = Department::factory()->make();
        $dto = DepartmentUpsertData::from([
            'name' => $data->name,
        ]);

        // act
        $this->service->update(department: $department, data: $dto);

        // assert
        $this->assertDatabaseHas(Department::class, [
            'id' => $department->id,
            'name' => $dto->name,
        ]);
    }

    public function test_delete_soft_deletes_department(): void
    {
        // arrange
        $department = Department::factory()->create();

        // act
        $this->service->delete($department);

        // assert
        $this->assertSoftDeleted(Department::class, [
            'id' => $department->id,
        ]);
    }

    public function test_paginate_after_creations_reflects_db_state(): void
    {
        // arrange
        Department::factory(3)->create();

        // act
        $paginationData = PaginationData::from();
        $this->service->paginate(data: $paginationData);

        // assert
        $this->assertDatabaseCount(Department::class, 3);
    }
}
