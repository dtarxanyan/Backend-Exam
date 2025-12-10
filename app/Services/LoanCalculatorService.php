<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\LoanAmortizationSchedule;
use App\Models\ExtraRepaymentSchedule;
use Illuminate\Support\Facades\DB;

class LoanCalculatorService
{
    /**
     * Calculate the monthly payment using the amortization formula.
     *
     * Formula: Monthly payment = (Loan amount * Monthly interest rate) / (1 - (1 + Monthly interest rate)^(-Number of months))
     *
     * @param float $loanAmount The principal loan amount
     * @param float $annualInterestRate The annual interest rate as a percentage
     * @param int $loanTermYears The loan term in years
     * @return float The calculated monthly payment
     */
    public function calculateMonthlyPayment(float $loanAmount, float $annualInterestRate, int $loanTermYears): float
    {
        $monthlyInterestRate = ($annualInterestRate / 12) / 100;
        $numberOfMonths = $loanTermYears * 12;

        if ($monthlyInterestRate == 0) {
            return $loanAmount / $numberOfMonths;
        }

        $monthlyPayment = ($loanAmount * $monthlyInterestRate) / 
                          (1 - pow(1 + $monthlyInterestRate, -$numberOfMonths));

        return round($monthlyPayment, 2);
    }

    /**
     * Calculate the effective interest rate.
     *
     * @param float $annualInterestRate The nominal annual interest rate
     * @param int $compoundingPeriodsPerYear Number of compounding periods (12 for monthly)
     * @return float The effective annual interest rate
     */
    public function calculateEffectiveInterestRate(float $annualInterestRate, int $compoundingPeriodsPerYear = 12): float
    {
        $nominalRate = $annualInterestRate / 100;
        $effectiveRate = pow(1 + ($nominalRate / $compoundingPeriodsPerYear), $compoundingPeriodsPerYear) - 1;
        
        return round($effectiveRate * 100, 4);
    }

    /**
     * Create a loan and generate both amortization schedules.
     *
     * @param array $data The loan data
     * @return Loan The created loan with schedules
     */
    public function createLoan(array $data): Loan
    {
        return DB::transaction(function () use ($data) {
            $loanAmount = (float) $data['loan_amount'];
            $annualInterestRate = (float) $data['annual_interest_rate'];
            $loanTermYears = (int) $data['loan_term_years'];
            $extraMonthlyPayment = (float) ($data['extra_monthly_payment'] ?? 0);

            $monthlyPayment = $this->calculateMonthlyPayment($loanAmount, $annualInterestRate, $loanTermYears);
            $effectiveInterestRate = $this->calculateEffectiveInterestRate($annualInterestRate);

            // Create the loan record
            $loan = Loan::create([
                'loan_amount' => $loanAmount,
                'annual_interest_rate' => $annualInterestRate,
                'loan_term_years' => $loanTermYears,
                'extra_monthly_payment' => $extraMonthlyPayment,
                'monthly_payment' => $monthlyPayment,
                'effective_interest_rate' => $effectiveInterestRate,
            ]);

            // Generate and store the standard amortization schedule
            $this->generateAndStoreAmortizationSchedule($loan);

            // Generate and store extra repayment schedule if extra payment is specified
            if ($extraMonthlyPayment > 0) {
                $this->generateAndStoreExtraRepaymentSchedule($loan);
            }

            return $loan->fresh(['amortizationSchedule', 'extraRepaymentSchedule']);
        });
    }

    /**
     * Generate and store the standard amortization schedule.
     *
     * @param Loan $loan The loan model
     * @return void
     */
    public function generateAndStoreAmortizationSchedule(Loan $loan): void
    {
        $balance = (float) $loan->loan_amount;
        $monthlyInterestRate = ((float) $loan->annual_interest_rate / 12) / 100;
        $numberOfMonths = $loan->loan_term_years * 12;
        $monthlyPayment = (float) $loan->monthly_payment;

        for ($month = 1; $month <= $numberOfMonths; $month++) {
            $startingBalance = $balance;
            $interestComponent = round($balance * $monthlyInterestRate, 2);
            $principalComponent = round($monthlyPayment - $interestComponent, 2);
            
            // Adjust for final payment
            if ($month == $numberOfMonths || $principalComponent > $balance) {
                $principalComponent = round($balance, 2);
                $monthlyPayment = round($principalComponent + $interestComponent, 2);
            }
            
            $endingBalance = round($balance - $principalComponent, 2);
            
            if ($endingBalance < 0) {
                $endingBalance = 0;
            }

            LoanAmortizationSchedule::create([
                'loan_id' => $loan->id,
                'month_number' => $month,
                'starting_balance' => round($startingBalance, 2),
                'monthly_payment' => $monthlyPayment,
                'principal_component' => $principalComponent,
                'interest_component' => $interestComponent,
                'ending_balance' => $endingBalance,
            ]);

            $balance = $endingBalance;

            if ($balance <= 0) {
                break;
            }
        }
    }

    /**
     * Generate and store the extra repayment schedule.
     *
     * @param Loan $loan The loan model
     * @return void
     */
    public function generateAndStoreExtraRepaymentSchedule(Loan $loan): void
    {
        $balance = (float) $loan->loan_amount;
        $monthlyInterestRate = ((float) $loan->annual_interest_rate / 12) / 100;
        $originalNumberOfMonths = $loan->loan_term_years * 12;
        $monthlyPayment = (float) $loan->monthly_payment;
        $extraPayment = (float) $loan->extra_monthly_payment;

        $month = 0;
        while ($balance > 0 && $month < $originalNumberOfMonths) {
            $month++;
            $startingBalance = $balance;
            $interestComponent = round($balance * $monthlyInterestRate, 2);
            $principalComponent = round($monthlyPayment - $interestComponent, 2);
            
            $actualExtraPayment = $extraPayment;
            $balanceAfterRegular = $balance - $principalComponent;
            
            // Adjust if regular payment exceeds balance
            if ($principalComponent > $balance) {
                $principalComponent = round($balance, 2);
                $balanceAfterRegular = 0;
                $actualExtraPayment = 0;
            }
            
            // Adjust extra payment if it exceeds remaining balance
            if ($actualExtraPayment > $balanceAfterRegular) {
                $actualExtraPayment = round($balanceAfterRegular, 2);
            }
            
            $endingBalanceAfterExtra = round($balanceAfterRegular - $actualExtraPayment, 2);
            
            if ($endingBalanceAfterExtra < 0) {
                $endingBalanceAfterExtra = 0;
            }

            // Calculate remaining loan term based on remaining balance
            $remainingTerm = 0;
            if ($endingBalanceAfterExtra > 0 && $monthlyInterestRate > 0) {
                $remainingTerm = (int) ceil(
                    -log(1 - ($endingBalanceAfterExtra * $monthlyInterestRate / $monthlyPayment)) / 
                    log(1 + $monthlyInterestRate)
                );
                
                if ($remainingTerm < 0 || !is_finite($remainingTerm)) {
                    $remainingTerm = 0;
                }
            }

            ExtraRepaymentSchedule::create([
                'loan_id' => $loan->id,
                'month_number' => $month,
                'starting_balance' => round($startingBalance, 2),
                'monthly_payment' => (float) $loan->monthly_payment, // Original monthly payment (unchanged)
                'principal_component' => $principalComponent,
                'interest_component' => $interestComponent,
                'extra_repayment_made' => round($actualExtraPayment, 2),
                'ending_balance_after_extra' => $endingBalanceAfterExtra,
                'remaining_loan_term' => $remainingTerm,
            ]);

            $balance = $endingBalanceAfterExtra;
        }
    }
}

