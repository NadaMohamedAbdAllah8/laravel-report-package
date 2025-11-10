<?php

namespace App\Services\Departments;

use App\Models\Department;
use App\Reports\PaginatedReportBuilder;
use Illuminate\Support\Collection;

class DepartmentReportService
{
    public function getGeneraReport(array $filterData): Collection
    {
        $employeesCount = function ($employees) {
            return $employees->count();
        };

        return (new PaginatedReportBuilder(query: Department::query()))
            ->attributes(attributes: ['id', 'name'])
            ->relationAttribute(key: 'manager_name', relationAttribute: 'manager.name')
            ->derivedAttribute(key: 'employees_count', lambdaFunction: $employeesCount)
            ->build()
            ->get();
    }
}
