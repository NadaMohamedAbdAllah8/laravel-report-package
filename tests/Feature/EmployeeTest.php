<?php

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Response;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    $this->route = '/api/employees/';
    $user = User::factory()->create();
    $this->actingAs($user, 'api');
});

test('index returns paginated employees', function (): void {
    $count = $this->faker->randomDigitNotZero();
    Employee::factory($count)->create();

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
});

test('store creates employee and returns json', function (): void {
    $data = Employee::factory()->make()->toArray();

    $response = $this->postJson($this->route, $data);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('item.name', $data['name'])
        ->assertJsonPath('item.email', $data['email'])
        ->assertJsonPath('item.department.id', $data['department_id']);
});

test('show returns employee json', function (): void {
    $employee = Employee::factory()->create();

    $response = $this->getJson($this->route.$employee->id);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('item.id', $employee->id)
        ->assertJsonPath('item.email', $employee->email)
        ->assertJsonPath('item.department.id', $employee->department_id);
});

test('show with negative id returns not found', function (): void {
    $response = $this->getJson($this->route.'-1');

    $response->assertStatus(Response::HTTP_NOT_FOUND);
});

test('update updates employee and returns json', function (): void {
    $employee = Employee::factory()->create();
    $updated = Employee::factory()->make();

    $data = [
        'name' => $updated->name,
        'email' => $updated->email,
    ];

    $response = $this->putJson($this->route.$employee->id, $data);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('item.id', $employee->id)
        ->assertJsonPath('item.name', $data['name'])
        ->assertJsonPath('item.email', $data['email'])
        ->assertJsonPath('item.department.id', $employee->department_id);
});

test('update with negative id returns not found', function (): void {
    $response = $this->putJson($this->route.'-1');

    $response->assertStatus(Response::HTTP_NOT_FOUND);
});

test('destroy deletes employee and returns json', function (): void {
    $employee = Employee::factory()->create();

    $response = $this->deleteJson($this->route.$employee->id);

    $response->assertStatus(Response::HTTP_NO_CONTENT);
});

test('destroy with negative id returns not found', function (): void {
    $response = $this->deleteJson($this->route.'-1');

    $response->assertStatus(Response::HTTP_NOT_FOUND);
});
