<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoanCalculatorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'loan_amount' => ['required', 'numeric', 'min:1', 'max:999999999999.99'],
            'annual_interest_rate' => ['required', 'numeric', 'min:0.01', 'max:100'],
            'loan_term_years' => ['required', 'integer', 'min:1', 'max:50'],
            'extra_monthly_payment' => ['nullable', 'numeric', 'min:0', 'max:999999999999.99'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'loan_amount.required' => 'The loan amount is required.',
            'loan_amount.numeric' => 'The loan amount must be a valid number.',
            'loan_amount.min' => 'The loan amount must be greater than zero.',

            'annual_interest_rate.required' => 'The annual interest rate is required.',
            'annual_interest_rate.numeric' => 'The annual interest rate must be a valid number.',
            'annual_interest_rate.min' => 'The annual interest rate must be greater than zero.',
            'annual_interest_rate.max' => 'The annual interest rate cannot exceed 100%.',

            'loan_term_years.required' => 'The loan term is required.',
            'loan_term_years.integer' => 'The loan term must be a whole number of years.',
            'loan_term_years.min' => 'The loan term must be at least 1 year.',
            'loan_term_years.max' => 'The loan term cannot exceed 50 years.',

            'extra_monthly_payment.numeric' => 'The extra monthly payment must be a valid number.',
            'extra_monthly_payment.min' => 'The extra monthly payment cannot be negative.',
        ];
    }

    /**
     * Handle a failed validation attempt for API requests.
     */
    protected function failedValidation(Validator $validator): void
    {
        if ($this->expectsJson()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }
}

