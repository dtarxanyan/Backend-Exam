<?php

namespace Tests\Feature;

use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanCalculatorWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_loan_calculator_index_page_loads(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Mortgage Loan Calculator');
    }

    public function test_can_submit_loan_calculation_form(): void
    {
        $response = $this->post('/loan/calculate', [
            'loan_amount' => 250000,
            'annual_interest_rate' => 5.5,
            'loan_term_years' => 30,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('loans', [
            'loan_amount' => 250000,
            'annual_interest_rate' => 5.5,
            'loan_term_years' => 30,
        ]);
    }

    public function test_can_submit_loan_calculation_with_extra_payment(): void
    {
        $response = $this->post('/loan/calculate', [
            'loan_amount' => 250000,
            'annual_interest_rate' => 5.5,
            'loan_term_years' => 30,
            'extra_monthly_payment' => 200,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('loans', ['loan_amount' => 250000, 'extra_monthly_payment' => 200]);
    }

    public function test_loan_show_page_displays_correctly(): void
    {
        $this->post('/loan/calculate', [
            'loan_amount' => 250000,
            'annual_interest_rate' => 5.5,
            'loan_term_years' => 30,
        ]);

        $loan = Loan::first();
        $response = $this->get("/loan/{$loan->id}");

        $response->assertStatus(200);
        $response->assertSee('$250,000.00');
        $response->assertSee('5.50%');
        $response->assertSee('30 Years');
    }

    public function test_validation_errors_displayed_for_invalid_input(): void
    {
        $response = $this->post('/loan/calculate', [
            'loan_amount' => -100000,
            'annual_interest_rate' => 5.5,
            'loan_term_years' => 30,
        ]);

        $response->assertSessionHasErrors(['loan_amount']);
    }

    public function test_can_delete_loan(): void
    {
        $this->post('/loan/calculate', [
            'loan_amount' => 100000,
            'annual_interest_rate' => 4.5,
            'loan_term_years' => 15,
        ]);

        $loan = Loan::first();
        $response = $this->delete("/loan/{$loan->id}");

        $response->assertRedirect('/');
        $this->assertDatabaseMissing('loans', ['id' => $loan->id]);
    }

    public function test_amortization_schedule_stored_in_database(): void
    {
        $this->post('/loan/calculate', [
            'loan_amount' => 100000,
            'annual_interest_rate' => 4.5,
            'loan_term_years' => 15,
        ]);

        $loan = Loan::first();
        $this->assertEquals(180, $loan->amortizationSchedule->count());

        $firstPayment = $loan->amortizationSchedule->first();
        $this->assertEquals(1, $firstPayment->month_number);
        $this->assertEquals(100000, $firstPayment->starting_balance);
    }

    public function test_extra_repayment_schedule_stored_when_extra_payment_provided(): void
    {
        $this->post('/loan/calculate', [
            'loan_amount' => 100000,
            'annual_interest_rate' => 4.5,
            'loan_term_years' => 15,
            'extra_monthly_payment' => 100,
        ]);

        $loan = Loan::first();
        $this->assertGreaterThan(0, $loan->extraRepaymentSchedule->count());
        $this->assertLessThan($loan->amortizationSchedule->count(), $loan->extraRepaymentSchedule->count());
    }
}

