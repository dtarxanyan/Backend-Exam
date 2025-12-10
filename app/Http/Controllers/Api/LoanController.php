<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoanCalculatorRequest;
use App\Models\Loan;
use App\Services\LoanCalculatorService;
use Illuminate\Http\JsonResponse;

class LoanController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected LoanCalculatorService $loanCalculatorService
    ) {}

    /**
     * Calculate loan and generate amortization schedules.
     *
     * @param LoanCalculatorRequest $request
     * @return JsonResponse
     */
    public function calculate(LoanCalculatorRequest $request): JsonResponse
    {
        try {
            $loan = $this->loanCalculatorService->createLoan($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Loan calculation completed successfully',
                'data' => [
                    'loan_id' => $loan->id,
                    'loan_details' => [
                        'loan_amount' => $loan->loan_amount,
                        'annual_interest_rate' => $loan->annual_interest_rate,
                        'loan_term_years' => $loan->loan_term_years,
                        'monthly_payment' => $loan->monthly_payment,
                        'effective_interest_rate' => $loan->effective_interest_rate,
                        'extra_monthly_payment' => $loan->extra_monthly_payment,
                    ],
                    'amortization_schedule' => $loan->amortizationSchedule,
                    'extra_repayment_schedule' => $loan->extraRepaymentSchedule->count() > 0 
                        ? $loan->extraRepaymentSchedule 
                        : null,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while calculating the loan',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Get a specific loan with its schedules.
     *
     * @param Loan $loan
     * @return JsonResponse
     */
    public function show(Loan $loan): JsonResponse
    {
        $loan->load(['amortizationSchedule', 'extraRepaymentSchedule']);

        return response()->json([
            'success' => true,
            'data' => [
                'loan_id' => $loan->id,
                'loan_details' => [
                    'loan_amount' => $loan->loan_amount,
                    'annual_interest_rate' => $loan->annual_interest_rate,
                    'loan_term_years' => $loan->loan_term_years,
                    'monthly_payment' => $loan->monthly_payment,
                    'effective_interest_rate' => $loan->effective_interest_rate,
                    'extra_monthly_payment' => $loan->extra_monthly_payment,
                ],
                'amortization_schedule' => $loan->amortizationSchedule,
                'extra_repayment_schedule' => $loan->extraRepaymentSchedule->count() > 0 
                    ? $loan->extraRepaymentSchedule 
                    : null,
            ],
        ]);
    }

    /**
     * List all loans.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $loans = Loan::with(['amortizationSchedule', 'extraRepaymentSchedule'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $loans,
        ]);
    }

    /**
     * Delete a loan and its schedules.
     *
     * @param Loan $loan
     * @return JsonResponse
     */
    public function destroy(Loan $loan): JsonResponse
    {
        $loan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Loan deleted successfully',
        ]);
    }

    /**
     * Get only the amortization schedule for a loan.
     *
     * @param Loan $loan
     * @return JsonResponse
     */
    public function amortizationSchedule(Loan $loan): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'loan_details' => [
                    'loan_amount' => $loan->loan_amount,
                    'annual_interest_rate' => $loan->annual_interest_rate,
                    'loan_term_years' => $loan->loan_term_years,
                    'monthly_payment' => $loan->monthly_payment,
                    'effective_interest_rate' => $loan->effective_interest_rate,
                ],
                'schedule' => $loan->amortizationSchedule,
            ],
        ]);
    }

    /**
     * Get only the extra repayment schedule for a loan.
     *
     * @param Loan $loan
     * @return JsonResponse
     */
    public function extraRepaymentSchedule(Loan $loan): JsonResponse
    {
        if ($loan->extraRepaymentSchedule->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No extra repayment schedule available for this loan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'loan_details' => [
                    'loan_amount' => $loan->loan_amount,
                    'annual_interest_rate' => $loan->annual_interest_rate,
                    'loan_term_years' => $loan->loan_term_years,
                    'monthly_payment' => $loan->monthly_payment,
                    'extra_monthly_payment' => $loan->extra_monthly_payment,
                    'effective_interest_rate' => $loan->effective_interest_rate,
                ],
                'schedule' => $loan->extraRepaymentSchedule,
            ],
        ]);
    }
}

