<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\Employees\EmployeeController;
use App\Http\Controllers\Api\Employees\Reports\EmployeeGeneralReportController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::apiResource('departments', DepartmentController::class);

    Route::prefix('employees')->group(function () {
        Route::apiResource('employees', EmployeeController::class);

        Route::prefix('report')->group(function () {
            Route::get('general', EmployeeGeneralReportController::class);
        });
    });
});
