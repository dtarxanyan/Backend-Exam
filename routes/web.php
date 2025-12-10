<?php

use App\Http\Controllers\LoanController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoanController::class, 'index'])->name('loan.index');
Route::post('/loan/calculate', [LoanController::class, 'calculate'])->name('loan.calculate');
Route::get('/loan/{loan}', [LoanController::class, 'show'])->name('loan.show');
Route::delete('/loan/{loan}', [LoanController::class, 'destroy'])->name('loan.destroy');
