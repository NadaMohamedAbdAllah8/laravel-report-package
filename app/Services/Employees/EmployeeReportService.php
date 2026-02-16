<?php

namespace App\Services\Employees;

use App\Models\Employee;
use Illuminate\Support\Collection;
use Nada\ReportBuilder\Reports\PaginatedReportBuilder;

class EmployeeReportService
{
    public function getGeneraReport(array $filterData): Collection
    {
        $perPage = isset($filterData['per_page']) && is_numeric($filterData['per_page'])
            ? max((int) $filterData['per_page'], 1)
            : PaginatedReportBuilder::DEFAULT_PER_PAGE;

        $page = isset($filterData['page']) && is_numeric($filterData['page'])
            ? max((int) $filterData['page'], 1)
            : PaginatedReportBuilder::DEFAULT_PAGE;

        return (new PaginatedReportBuilder(query: Employee::query()->orderByDesc('id')))
            ->paginate(perPage: $perPage, page: $page)
            ->attributes(attributes: ['id', 'name', 'email'])
            ->relationAttribute(key: 'department', relationAttribute: 'department.name')
            ->relationAttribute(key: 'manager', relationAttribute: 'manager.name')
            ->build()
            ->get();
    }
}
