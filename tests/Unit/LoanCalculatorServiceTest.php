<?php

namespace Tests\Unit;

use App\Services\LoanCalculatorService;
use PHPUnit\Framework\TestCase;

class LoanCalculatorServiceTest extends TestCase
{
    private LoanCalculatorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LoanCalculatorService();
    }

    public function test_calculate_monthly_payment(): void
    {
        $monthlyPayment = $this->service->calculateMonthlyPayment(250000, 5.5, 30);
        $this->assertEqualsWithDelta(1419.47, $monthlyPayment, 0.01);
    }

    public function test_calculate_monthly_payment_different_values(): void
    {
        $monthlyPayment = $this->service->calculateMonthlyPayment(100000, 4.5, 15);
        $this->assertEqualsWithDelta(764.99, $monthlyPayment, 0.01);
    }

    public function test_calculate_monthly_payment_small_loan(): void
    {
        $monthlyPayment = $this->service->calculateMonthlyPayment(10000, 6, 5);
        $this->assertEqualsWithDelta(193.33, $monthlyPayment, 0.01);
    }

    public function test_calculate_effective_interest_rate(): void
    {
        $effectiveRate = $this->service->calculateEffectiveInterestRate(5.5);
        $this->assertEqualsWithDelta(5.6408, $effectiveRate, 0.01);
    }

    public function test_calculate_effective_interest_rate_different_values(): void
    {
        $effectiveRate = $this->service->calculateEffectiveInterestRate(10);
        $this->assertEqualsWithDelta(10.4713, $effectiveRate, 0.01);
    }

    public function test_higher_interest_rate_results_in_higher_payment(): void
    {
        $paymentLowRate = $this->service->calculateMonthlyPayment(100000, 3.0, 30);
        $paymentHighRate = $this->service->calculateMonthlyPayment(100000, 6.0, 30);

        $this->assertGreaterThan($paymentLowRate, $paymentHighRate);
    }

    public function test_shorter_term_results_in_higher_payment(): void
    {
        $payment30Year = $this->service->calculateMonthlyPayment(100000, 5.0, 30);
        $payment15Year = $this->service->calculateMonthlyPayment(100000, 5.0, 15);

        $this->assertGreaterThan($payment30Year, $payment15Year);
    }

    public function test_larger_loan_results_in_higher_payment(): void
    {
        $paymentSmallLoan = $this->service->calculateMonthlyPayment(100000, 5.0, 30);
        $paymentLargeLoan = $this->service->calculateMonthlyPayment(300000, 5.0, 30);

        $this->assertGreaterThan($paymentSmallLoan, $paymentLargeLoan);
    }
}

