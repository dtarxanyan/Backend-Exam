<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoanCalculatorRequest;
use App\Models\Loan;
use App\Services\LoanCalculatorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LoanController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected LoanCalculatorService $loanCalculatorService
    ) {}

    /**
     * Display the loan calculator form.
     *
     * @return View
     */
    public function index(): View
    {
        $loans = Loan::orderBy('created_at', 'desc')->take(5)->get();
        
        return view('loan.index', compact('loans'));
    }

    /**
     * Calculate loan and store the result.
     *
     * @param LoanCalculatorRequest $request
     * @return RedirectResponse
     */
    public function calculate(LoanCalculatorRequest $request): RedirectResponse
    {
        try {
            $loan = $this->loanCalculatorService->createLoan($request->validated());
            
            return redirect()
                ->route('loan.show', $loan)
                ->with('success', 'Loan calculation completed successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'An error occurred while calculating the loan. Please try again.');
        }
    }

    /**
     * Display the loan details and schedules.
     *
     * @param Loan $loan
     * @return View
     */
    public function show(Loan $loan): View
    {
        $loan->load(['amortizationSchedule', 'extraRepaymentSchedule']);

        return view('loan.show', compact('loan'));
    }

    /**
     * Delete a loan.
     *
     * @param Loan $loan
     * @return RedirectResponse
     */
    public function destroy(Loan $loan): RedirectResponse
    {
        $loan->delete();

        return redirect()
            ->route('loan.index')
            ->with('success', 'Loan deleted successfully!');
    }
}

