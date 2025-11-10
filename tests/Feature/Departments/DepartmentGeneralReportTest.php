<?php

namespace Tests\Feature\Departments;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Tests\TestCase;

class DepartmentGeneralReportTest extends TestCase
{
    private string $route = '/api/departments/report/general';

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

        // Create a manager for the department
        $manager = Employee::factory()
            ->forDepartment($department)
            ->create([
                'title' => 'Manager',
                'manager_id' => null,
            ]);

        // Create two employees under the manager
        Employee::factory(2)
            ->forDepartment($department)
            ->state([
                'manager_id' => $manager->id,
            ])
            ->create();

        $expectedCount = 3; // manager + 2 employees

        // act
        $response = $this->getJson($this->route);

        // assert
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
        $this->assertNotNull($row, 'Report contains created department');
        $this->assertEquals($department->name, $row['name']);
        $this->assertEquals($expectedCount, (int) $row['employees_count']);
    }
}
