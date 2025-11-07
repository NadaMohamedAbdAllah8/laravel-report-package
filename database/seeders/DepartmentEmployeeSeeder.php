<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class DepartmentEmployeeSeeder extends Seeder
{
    /**
     * Seed 4 departments with employees and a manager each.
     */
    public function run(): void
    {
        if (Department::count() > 0 || Employee::count() > 0) {
            return;
        }

        $sizes = [20, 50, 30, 15];

        foreach ($sizes as $size) {
            $department = Department::factory()->create();

            $manager = Employee::factory()
                ->for($department)
                ->create([
                    'title' => 'Manager',
                    'manager_id' => null,
                ]);

            if ($size > 1) {
                $employees = Employee::factory($size - 1)
                    ->for($department)
                    ->state([
                        'manager_id' => $manager->id,
                    ])
                    ->create();
            }
        }
    }
}
