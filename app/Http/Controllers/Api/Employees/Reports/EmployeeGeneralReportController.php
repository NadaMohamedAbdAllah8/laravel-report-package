<?php

namespace App\Http\Controllers\Api\Employees\Reports;

use App\Http\Controllers\Controller;
use App\Services\EmployeeReportService;
use App\Traits\RespondsWithJson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeGeneralReportController extends Controller
{
    use RespondsWithJson;

    public function __construct(private EmployeeReportService $reports) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = $this->reports->getGeneraReport(filterData: $request->all());

        return $this->returnItemsWithSuccessMessage(
            items: $data,
            message: 'Employee report retrieved successfully.',
        );
    }
}
