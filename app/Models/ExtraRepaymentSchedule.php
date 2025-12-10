<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtraRepaymentSchedule extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'extra_repayment_schedule';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'loan_id',
        'month_number',
        'starting_balance',
        'monthly_payment',
        'principal_component',
        'interest_component',
        'extra_repayment_made',
        'ending_balance_after_extra',
        'remaining_loan_term',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'month_number' => 'integer',
        'starting_balance' => 'decimal:2',
        'monthly_payment' => 'decimal:2',
        'principal_component' => 'decimal:2',
        'interest_component' => 'decimal:2',
        'extra_repayment_made' => 'decimal:2',
        'ending_balance_after_extra' => 'decimal:2',
        'remaining_loan_term' => 'integer',
    ];

    /**
     * Get the loan that owns the schedule entry.
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}

