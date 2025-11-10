<?php

namespace Tests\Feature\Employees;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Tests\TestCase;

class EmployeeGeneralReportTest extends TestCase
{
    private string $route = '/api/employees/report/general';

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
    }

    public function test_general_report_returns_expected_structure_and_data(): void
    {
        // arrange
        $department = Department::factory()->create();
        $manager = Employee::factory()
            ->forDepartment($department)
            ->create([
                'title' => 'Manager',
                'manager_id' => null,
            ]);

        $employees = Employee::factory(2)
            ->forDepartment($department)
            ->state([
                'manager_id' => $manager->id,
            ])
            ->create();

        $all = collect([$manager])->merge($employees)->values();

        // act
        $response = $this->getJson($this->route);

        // assert
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
        $this->assertNotEmpty($payload);

        $byId = collect($payload)->keyBy('id');

        foreach ($all as $emp) {
            $this->assertArrayHasKey($emp->id, $byId, 'Report contains employee id');
            $row = $byId[$emp->id];
            $this->assertEquals($emp->name, $row['name']);
            $this->assertEquals($emp->email, $row['email']);
            $this->assertEquals($department->name, $row['department']);
            $expectedManagerName = $emp->manager_id ? $manager->name : null;
            $this->assertEquals($expectedManagerName, $row['manager']);
        }
    }
}
