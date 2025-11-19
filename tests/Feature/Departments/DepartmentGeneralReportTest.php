<?php

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    $this->route = '/api/departments/report/general';
    $user = User::factory()->create();
    $this->actingAs($user, 'api');
});

test('general report returns expected structure and data', function (): void {
    $department = Department::factory()->create();

    $manager = Employee::factory()
        ->forDepartment($department)
        ->create([
            'title' => 'Manager',
            'manager_id' => null,
        ]);

    Employee::factory(2)
        ->forDepartment($department)
        ->state([
            'manager_id' => $manager->id,
        ])
        ->create();

    $expectedCount = 3;

    $response = $this->getJson($this->route);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Department report retrieved successfully.')
        ->assertJsonStructure([
            'success',
            'message',
            'items' => [
                'data',
                'expressionValues',
                'pagination' => [
                    'size', 'page', 'total_pages', 'total_size', 'per_page',
                ],
            ],
        ]);

    $rows = collect($response->json('items.data'));
    $row = $rows->firstWhere('id', $department->id);
    expect($row)->not()->toBeNull();
    expect($row['name'])->toBe($department->name);
    expect((int) $row['employees_count'])->toBe($expectedCount);
});
