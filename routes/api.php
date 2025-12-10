<?php

use App\Http\Controllers\Api\LoanController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/loans/calculate', [LoanController::class, 'calculate']);
    Route::get('/loans', [LoanController::class, 'index']);
    Route::get('/loans/{loan}', [LoanController::class, 'show']);
    Route::delete('/loans/{loan}', [LoanController::class, 'destroy']);
    Route::get('/loans/{loan}/amortization-schedule', [LoanController::class, 'amortizationSchedule']);
    Route::get('/loans/{loan}/extra-repayment-schedule', [LoanController::class, 'extraRepaymentSchedule']);
});

