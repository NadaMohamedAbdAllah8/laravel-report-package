<?php

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Response;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    $this->route = '/api/departments/';
    $user = User::factory()->create();
    $this->actingAs($user, 'api');
});

test('index returns paginated departments', function (): void {
    $count = $this->faker->randomDigitNotZero();
    Department::factory($count)->create();

    $perPage = $this->faker->randomDigitNotZero();
    $response = $this->getJson($this->route.'?per_page='.$perPage);

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
});

test('store creates department and returns json', function (): void {
    $data = Department::factory()->make()->toArray();

    $response = $this->postJson($this->route, $data);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('item.name', $data['name']);
});

test('show returns department json', function (): void {
    $department = Department::factory()->create();

    $response = $this->getJson($this->route.$department->id);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('item.id', $department->id)
        ->assertJsonPath('item.name', $department->name);
});

test('show with negative id returns not found', function (): void {
    $response = $this->getJson($this->route.'-1');

    $response->assertStatus(Response::HTTP_NOT_FOUND);
});

test('update updates department and returns json', function (): void {
    $department = Department::factory()->create();
    $updated = Department::factory()->make();

    $data = [
        'name' => $updated->name,
    ];

    $response = $this->putJson($this->route.$department->id, $data);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('item.id', $department->id)
        ->assertJsonPath('item.name', $data['name']);
});

test('update with negative id returns not found', function (): void {
    $response = $this->putJson($this->route.'-1');

    $response->assertStatus(Response::HTTP_NOT_FOUND);
});

test('destroy deletes department and returns json', function (): void {
    $department = Department::factory()->create();

    $response = $this->deleteJson($this->route.$department->id);

    $response->assertStatus(Response::HTTP_NO_CONTENT);
});

test('destroy with negative id returns not found', function (): void {
    $response = $this->deleteJson($this->route.'-1');

    $response->assertStatus(Response::HTTP_NOT_FOUND);
});
