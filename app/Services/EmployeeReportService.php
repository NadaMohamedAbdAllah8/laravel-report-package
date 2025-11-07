<?php

namespace App\Services;

use App\Models\Employee;
use App\Reports\PaginatedReportBuilder;
use Illuminate\Support\Collection;

class EmployeeReportService
{
    public function getReport(): Collection
    {
        return (new PaginatedReportBuilder(query: Employee::query()))
            ->attributes(['id', 'name', 'email'])
            ->relationAttribute('department', 'department.name')
            ->relationAttribute('manager', 'manager.name')
            ->build()
            ->get();
    }
}
