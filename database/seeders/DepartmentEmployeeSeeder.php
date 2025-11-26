<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class DepartmentEmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $departmentCount = 20;
        $minEmployeesPerDepartment = 2000;
        $maxEmployeesPerDepartment = 5000;

        if (Department::count() >= $departmentCount || Employee::count() > $minEmployeesPerDepartment * $departmentCount) {
            return;
        }

        $departments = Department::factory($departmentCount)
            ->create()
            ->each(function (Department $department) use ($minEmployeesPerDepartment, $maxEmployeesPerDepartment) {
                $employeesCount = fake()->numberBetween(
                    $minEmployeesPerDepartment,
                    $maxEmployeesPerDepartment
                );

                // 1) Create manager
                $manager = Employee::factory()
                    ->for($department)
                    ->create([
                        'department_id' => $department->id,
                        'title' => 'Manager',
                        'manager_id' => null,
                    ]);

                // 2) Create the rest of the employees
                if ($employeesCount > 1) {
                    $employees = Employee::factory($employeesCount - 1)
                        ->for($department)
                        ->create([
                            'department_id' => $department->id,
                            'manager_id' => $manager->id,
                        ]);
                }
            });
    }
}
