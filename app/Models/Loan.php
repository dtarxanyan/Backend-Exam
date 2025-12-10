<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'loan_amount',
        'annual_interest_rate',
        'loan_term_years',
        'extra_monthly_payment',
        'monthly_payment',
        'effective_interest_rate',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'loan_amount' => 'decimal:2',
        'annual_interest_rate' => 'decimal:2',
        'loan_term_years' => 'integer',
        'extra_monthly_payment' => 'decimal:2',
        'monthly_payment' => 'decimal:2',
        'effective_interest_rate' => 'decimal:4',
    ];

    /**
     * Get the amortization schedule for the loan.
     */
    public function amortizationSchedule(): HasMany
    {
        return $this->hasMany(LoanAmortizationSchedule::class)->orderBy('month_number');
    }

    /**
     * Get the extra repayment schedule for the loan.
     */
    public function extraRepaymentSchedule(): HasMany
    {
        return $this->hasMany(ExtraRepaymentSchedule::class)->orderBy('month_number');
    }
}

