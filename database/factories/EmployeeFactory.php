<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Employee>
     */
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'department_id' => Department::factory()->create()->id,
            'manager_id' => null,
            'salary' => $this->faker->randomFloat(2, 30000, 200000),
            'title' => $this->faker->jobTitle(),
        ];
    }

    /**
     * Assign a manager to the employee.
     */
    public function withManager(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'manager_id' => Employee::factory()->state([
                    'department_id' => $attributes['department_id'] ?? Department::factory()->create()->id,
                ]),
            ];
        });
    }

    /**
     * Put the employee in a specific department.
     */
    public function forDepartment(Department $department): static
    {
        return $this->state(fn(array $attributes) => [
            'department_id' => $department->id,
        ]);
    }
}
