<?php

namespace App\Http\Controllers\Api\Departments\Reports;

use App\Http\Controllers\Controller;
use App\Services\Departments\DepartmentReportService;
use App\Traits\RespondsWithJson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentGeneralReportController extends Controller
{
    use RespondsWithJson;

    public function __construct(private DepartmentReportService $reports) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = $this->reports->getGeneraReport(filterData: $request->all());

        return $this->returnItemsWithSuccessMessage(
            items: $data,
            message: 'Department report retrieved successfully.',
        );
    }
}
