<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EmployeeReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmployeeReportController extends Controller
{
    public function __construct(private EmployeeReportService $reports) {}

    public function show(Request $request): JsonResponse
    {
        $data = $this->reports->getReport($request->all());

        // Return raw data from the service without wrapping in a resource
        return response()->json($data, Response::HTTP_OK);
    }
}
