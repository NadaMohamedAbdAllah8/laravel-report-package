<?php

namespace App\Services;

use App\Models\Employee;
use App\Reports\PaginatedReportBuilder;
use Illuminate\Support\Collection;

class EmployeeReportService
{
    public function getGeneraReport(array $filterData): Collection
    {
        return (new PaginatedReportBuilder(query: Employee::query()))
            ->attributes(attributes: ['id', 'name', 'email'])
            ->relationAttribute(key: 'department', relationAttribute: 'department.name')
            ->relationAttribute(key: 'manager', relationAttribute: 'manager.name')
            ->build()
            ->get();
    }
}
