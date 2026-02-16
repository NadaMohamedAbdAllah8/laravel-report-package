<?php

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    $this->route = '/api/employees/report/general?per_page=100';
    $user = User::factory()->create();
    $this->actingAs($user, 'api');
});

test('general report returns expected structure and data', function (): void {
    $department = Department::factory()->create();
    $manager = Employee::factory()
        ->for($department)
        ->create([
            'title' => 'Manager',
            'manager_id' => null,
        ]);

    $employees = Employee::factory(2)
        ->for($department)
        ->state([
            'manager_id' => $manager->id,
        ])
        ->create();

    $all = collect([$manager])->merge($employees)->values();

    $response = $this->getJson($this->route);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Employee report retrieved successfully.')
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

    $payload = $response->json('items.data');
    expect($payload)->not()->toBeEmpty();

    foreach ($all as $emp) {
        $row = collect($payload)->firstWhere('id', $emp->id);
        expect($row)->not()->toBeNull();
        expect($row['name'])->toBe($emp->name);
        expect($row['email'])->toBe($emp->email);
        expect($row['department'])->toBe($department->name);
        $expectedManagerName = $emp->manager_id ? $manager->name : null;
        expect($row['manager'])->toBe($expectedManagerName);
    }
});
