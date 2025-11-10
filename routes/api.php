<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\Departments\Reports\DepartmentGeneralReportController;
use App\Http\Controllers\Api\Employees\EmployeeController;
use App\Http\Controllers\Api\Employees\Reports\EmployeeGeneralReportController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::apiResource('departments', DepartmentController::class);
    Route::prefix('departments/report')->as('departments.report.')->group(function () {
        Route::get('general', DepartmentGeneralReportController::class);
    });

    Route::apiResource('employees', EmployeeController::class);
    Route::prefix('employees/report')->as('employees.report.')->group(function () {
        Route::get('general', EmployeeGeneralReportController::class);
    });
});
