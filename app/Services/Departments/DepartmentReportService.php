<?php

namespace App\Services\Departments;

use App\Models\Department;
use App\Reports\PaginatedReportBuilder;
use App\Services\Departments\Dto\DepartmentEmployeeData;
use Illuminate\Support\Collection;

class DepartmentReportService
{
    public function getGeneraReport(array $filterData): Collection
    {
        $employeesCount = function ($employees): int {
            return $employees->count();
        };

        return (new PaginatedReportBuilder(query: Department::query()))
            ->attributes(attributes: ['id', 'name'])
            ->relationAttribute(key: 'manager_name', relationAttribute: 'manager.name')
            ->derivedAttribute(key: 'employees_count', lambdaFunction: $employeesCount)
            ->build()
            ->get();
    }

    public function getSalariesReport(array $filterData): Collection
    {
        $highestPaidEmployee = function ($employees): DepartmentEmployeeData {
            return DepartmentEmployeeData::from($employees->sortByDesc('salary')->first());
        };

        $lowestPaidEmployee = function ($employees): DepartmentEmployeeData {
            return DepartmentEmployeeData::from($employees->sortBy('salary')->first());
        };

        return (new PaginatedReportBuilder(query: Department::query()))
            ->attributes(attributes: ['id', 'name'])
            ->derivedAttribute(key: 'highest_paid_employee', lambdaFunction: $highestPaidEmployee)
            ->derivedAttribute(key: 'lowest_paid_employee', lambdaFunction: $lowestPaidEmployee)
            ->build()
            ->get();
    }
}
