<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Response;
use Tests\TestCase;

class DepartmentTest extends TestCase
{
    private string $route = '/api/departments/';

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
    }

    public function test_index_returns_paginated_departments(): void
    {
        // arrange
        $count = $this->faker->randomDigitNotZero();
        Department::factory($count)->create();

        // act
        $perPage = $this->faker->randomDigitNotZero();
        $response = $this->getJson($this->route.'?per_page='.$perPage);

        // assert
        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'items' => [
                    [
                        'id',
                        'name',
                    ],
                ],
                'size',
                'page',
                'total_pages',
                'total_size',
                'per_page',
            ]);
    }

    public function test_store_creates_department_and_returns_json(): void
    {
        // arrange
        $data = Department::factory()->make()->toArray();

        // act
        $response = $this->postJson($this->route, $data);

        // assert
        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('item.name', $data['name']);
    }

    public function test_show_returns_department_json(): void
    {
        // arrange
        $department = Department::factory()->create();

        // act
        $response = $this->getJson($this->route.$department->id);

        // assert
        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('item.id', $department->id)
            ->assertJsonPath('item.name', $department->name);
    }

    public function test_show_with_negative_id_returns_not_found(): void
    {
        // act
        $response = $this->getJson($this->route.'-1');

        // assert
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_update_updates_department_and_returns_json(): void
    {
        // arrange
        $department = Department::factory()->create();
        $updated = Department::factory()->make();

        $data = [
            'name' => $updated->name,
        ];

        // act
        $response = $this->putJson($this->route.$department->id, $data);

        // assert
        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('item.id', $department->id)
            ->assertJsonPath('item.name', $data['name']);
    }

    public function test_update_with_negative_id_returns_not_found(): void
    {
        // act
        $response = $this->putJson($this->route.'-1');

        // assert
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_destroy_deletes_department_and_returns_json(): void
    {
        // arrange
        $department = Department::factory()->create();

        // act
        $response = $this->deleteJson($this->route.$department->id);

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
