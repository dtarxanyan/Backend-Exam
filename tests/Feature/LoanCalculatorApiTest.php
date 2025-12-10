<?php

namespace Tests\Feature;

use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanCalculatorApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_calculate_loan_successfully(): void
    {
        $response = $this->postJson('/api/v1/loans/calculate', [
            'loan_amount' => 250000,
            'annual_interest_rate' => 5.5,
            'loan_term_years' => 30,
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true, 'message' => 'Loan calculation completed successfully'])
            ->assertJsonStructure([
                'data' => [
                    'loan_id',
                    'loan_details' => ['loan_amount', 'annual_interest_rate', 'loan_term_years', 'monthly_payment', 'effective_interest_rate'],
                    'amortization_schedule',
                ],
            ]);
    }

    public function test_can_calculate_loan_with_extra_payment(): void
    {
        $response = $this->postJson('/api/v1/loans/calculate', [
            'loan_amount' => 250000,
            'annual_interest_rate' => 5.5,
            'loan_term_years' => 30,
            'extra_monthly_payment' => 200,
        ]);

        $response->assertStatus(201)->assertJson(['success' => true]);
        
        $data = $response->json('data');
        $this->assertNotNull($data['extra_repayment_schedule']);
        $this->assertLessThan(count($data['amortization_schedule']), count($data['extra_repayment_schedule']));
    }

    public function test_validation_fails_for_negative_loan_amount(): void
    {
        $response = $this->postJson('/api/v1/loans/calculate', [
            'loan_amount' => -100000,
            'annual_interest_rate' => 5.5,
            'loan_term_years' => 30,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['loan_amount']);
    }

    public function test_validation_fails_for_zero_loan_amount(): void
    {
        $response = $this->postJson('/api/v1/loans/calculate', [
            'loan_amount' => 0,
            'annual_interest_rate' => 5.5,
            'loan_term_years' => 30,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['loan_amount']);
    }

    public function test_validation_fails_for_negative_interest_rate(): void
    {
        $response = $this->postJson('/api/v1/loans/calculate', [
            'loan_amount' => 250000,
            'annual_interest_rate' => -5,
            'loan_term_years' => 30,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['annual_interest_rate']);
    }

    public function test_validation_fails_for_zero_interest_rate(): void
    {
        $response = $this->postJson('/api/v1/loans/calculate', [
            'loan_amount' => 250000,
            'annual_interest_rate' => 0,
            'loan_term_years' => 30,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['annual_interest_rate']);
    }

    public function test_validation_fails_for_negative_loan_term(): void
    {
        $response = $this->postJson('/api/v1/loans/calculate', [
            'loan_amount' => 250000,
            'annual_interest_rate' => 5.5,
            'loan_term_years' => -5,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['loan_term_years']);
    }

    public function test_validation_fails_for_zero_loan_term(): void
    {
        $response = $this->postJson('/api/v1/loans/calculate', [
            'loan_amount' => 250000,
            'annual_interest_rate' => 5.5,
            'loan_term_years' => 0,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['loan_term_years']);
    }

    public function test_validation_fails_for_negative_extra_payment(): void
    {
        $response = $this->postJson('/api/v1/loans/calculate', [
            'loan_amount' => 250000,
            'annual_interest_rate' => 5.5,
            'loan_term_years' => 30,
            'extra_monthly_payment' => -100,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['extra_monthly_payment']);
    }

    public function test_validation_fails_for_missing_required_fields(): void
    {
        $response = $this->postJson('/api/v1/loans/calculate', []);

        $response->assertStatus(422)->assertJsonValidationErrors(['loan_amount', 'annual_interest_rate', 'loan_term_years']);
    }

    public function test_can_retrieve_specific_loan(): void
    {
        $createResponse = $this->postJson('/api/v1/loans/calculate', [
            'loan_amount' => 100000,
            'annual_interest_rate' => 4.5,
            'loan_term_years' => 15,
        ]);

        $loanId = $createResponse->json('data.loan_id');
        $response = $this->getJson("/api/v1/loans/{$loanId}");

        $response->assertStatus(200)->assertJson(['success' => true, 'data' => ['loan_id' => $loanId]]);
    }

    public function test_can_list_all_loans(): void
    {
        $this->postJson('/api/v1/loans/calculate', ['loan_amount' => 100000, 'annual_interest_rate' => 4.5, 'loan_term_years' => 15]);
        $this->postJson('/api/v1/loans/calculate', ['loan_amount' => 200000, 'annual_interest_rate' => 5.0, 'loan_term_years' => 20]);

        $response = $this->getJson('/api/v1/loans');
        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_can_delete_loan(): void
    {
        $createResponse = $this->postJson('/api/v1/loans/calculate', [
            'loan_amount' => 100000,
            'annual_interest_rate' => 4.5,
            'loan_term_years' => 15,
        ]);

        $loanId = $createResponse->json('data.loan_id');
        $response = $this->deleteJson("/api/v1/loans/{$loanId}");

        $response->assertStatus(200)->assertJson(['success' => true, 'message' => 'Loan deleted successfully']);
        $this->assertDatabaseMissing('loans', ['id' => $loanId]);
    }

    public function test_can_retrieve_amortization_schedule(): void
    {
        $createResponse = $this->postJson('/api/v1/loans/calculate', [
            'loan_amount' => 100000,
            'annual_interest_rate' => 4.5,
            'loan_term_years' => 15,
        ]);

        $loanId = $createResponse->json('data.loan_id');
        $response = $this->getJson("/api/v1/loans/{$loanId}/amortization-schedule");

        $response->assertStatus(200)->assertJsonStructure(['success', 'data' => ['loan_details', 'schedule']]);
    }

    public function test_can_retrieve_extra_repayment_schedule(): void
    {
        $createResponse = $this->postJson('/api/v1/loans/calculate', [
            'loan_amount' => 100000,
            'annual_interest_rate' => 4.5,
            'loan_term_years' => 15,
            'extra_monthly_payment' => 100,
        ]);

        $loanId = $createResponse->json('data.loan_id');
        $response = $this->getJson("/api/v1/loans/{$loanId}/extra-repayment-schedule");

        $response->assertStatus(200)->assertJsonStructure(['success', 'data' => ['loan_details', 'schedule']]);
    }

    public function test_extra_repayment_schedule_returns_404_when_no_extra_payment(): void
    {
        $createResponse = $this->postJson('/api/v1/loans/calculate', [
            'loan_amount' => 100000,
            'annual_interest_rate' => 4.5,
            'loan_term_years' => 15,
        ]);

        $loanId = $createResponse->json('data.loan_id');
        $response = $this->getJson("/api/v1/loans/{$loanId}/extra-repayment-schedule");

        $response->assertStatus(404)->assertJson(['success' => false]);
    }
}

