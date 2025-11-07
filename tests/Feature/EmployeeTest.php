<?php

namespace Tests\Feature;

use App\Models\Employee;
use Illuminate\Http\Response;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    private string $route = '/api/employees/';

    public function test_index_returns_paginated_employees(): void
    {
        // arrange
        $count = $this->faker->randomDigitNotZero();
        Employee::factory($count)->create();

        // act
        $perPage = $this->faker->randomDigitNotZero();
        $response = $this->getJson($this->route.'?per_page='.$perPage);

        // assert
        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Employees retrieved')
            ->assertJsonStructure([
                'success',
                'message',
                'items' => [
                    [
                        'id',
                        'name',
                        'address',
                        'phone',
                        'email',
                        'salary',
                        'title',
                        'department' => ['id', 'name'],
                    ],

                ],
                'size',
                'page',
                'total_pages',
                'total_size',
                'per_page',
            ]);
    }

    public function test_store_creates_employee_and_returns_json(): void
    {
        // arrange
        $data = Employee::factory()->make()->toArray();

        // act
        $response = $this->postJson($this->route, $data);

        // assert
        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Employee created')
            ->assertJsonPath('item.name', $data['name'])
            ->assertJsonPath('item.email', $data['email'])
            ->assertJsonPath('item.department.id', $data['department_id']);
    }

    public function test_show_returns_employee_json(): void
    {
        // arrange
        $employee = Employee::factory()->create();

        // act
        $response = $this->getJson($this->route.$employee->id);

        // assert
        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Employee retrieved')
            ->assertJsonPath('item.id', $employee->id)
            ->assertJsonPath('item.email', $employee->email)
            ->assertJsonPath('item.department.id', $employee->department_id);
    }

    public function test_show_with_negative_id_returns_not_found(): void
    {
        // act
        $response = $this->getJson($this->route.'-1');

        // assert
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_update_updates_employee_and_returns_json(): void
    {
        // arrange
        $employee = Employee::factory()->create();
        $updated = Employee::factory()->make();

        $data = [
            'name' => $updated->name,
            'email' => $updated->email,
        ];

        // act
        $response = $this->putJson($this->route.$employee->id, $data);

        // assert
        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Employee updated')
            ->assertJsonPath('item.id', $employee->id)
            ->assertJsonPath('item.name', $data['name'])
            ->assertJsonPath('item.email', $data['email'])
            ->assertJsonPath('item.department.id', $employee->department_id);
    }

    public function test_update_with_negative_id_returns_not_found(): void
    {
        // act
        $response = $this->putJson($this->route.'-1');

        // assert
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_destroy_deletes_employee_and_returns_json(): void
    {
        // arrange
        $employee = Employee::factory()->create();

        // act
        $response = $this->deleteJson($this->route.$employee->id);

        // assert
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function test_destroy_with_negative_id_returns_not_found(): void
    {
        // act
        $response = $this->deleteJson($this->route.'-1');

        // assert
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
