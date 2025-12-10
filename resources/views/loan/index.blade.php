@extends('layouts.app')

@section('title', 'Calculate Loan')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="icon">💰</div>
            <div>
                <h2>Loan Details</h2>
                <p>Enter your loan information to calculate the amortization schedule</p>
            </div>
        </div>

        <form action="{{ route('loan.calculate') }}" method="POST">
            @csrf
            
            <div class="form-grid">
                <div class="form-group @error('loan_amount') error @enderror">
                    <label for="loan_amount">Loan Amount ($)</label>
                    <input type="number" id="loan_amount" name="loan_amount" value="{{ old('loan_amount', 250000) }}" placeholder="e.g., 250000" step="0.01" min="1" required>
                    <span class="hint">The principal amount you want to borrow</span>
                    @error('loan_amount')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group @error('annual_interest_rate') error @enderror">
                    <label for="annual_interest_rate">Annual Interest Rate (%)</label>
                    <input type="number" id="annual_interest_rate" name="annual_interest_rate" value="{{ old('annual_interest_rate', 5.5) }}" placeholder="e.g., 5.5" step="0.01" min="0.01" max="100" required>
                    <span class="hint">The yearly interest rate percentage</span>
                    @error('annual_interest_rate')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group @error('loan_term_years') error @enderror">
                    <label for="loan_term_years">Loan Term (Years)</label>
                    <input type="number" id="loan_term_years" name="loan_term_years" value="{{ old('loan_term_years', 30) }}" placeholder="e.g., 30" min="1" max="50" required>
                    <span class="hint">Duration of the loan in years</span>
                    @error('loan_term_years')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group @error('extra_monthly_payment') error @enderror">
                    <label for="extra_monthly_payment">Extra Monthly Payment ($) <span style="color: var(--text-muted)">- Optional</span></label>
                    <input type="number" id="extra_monthly_payment" name="extra_monthly_payment" value="{{ old('extra_monthly_payment') }}" placeholder="e.g., 200" step="0.01" min="0">
                    <span class="hint">Additional monthly payment to reduce principal faster</span>
                    @error('extra_monthly_payment')<span class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>

            <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary">📊 Calculate Schedule</button>
                <button type="reset" class="btn btn-secondary">🔄 Reset</button>
            </div>
        </form>
    </div>

    @if($loans->count() > 0)
        <div class="card">
            <div class="card-header">
                <div class="icon">📋</div>
                <div>
                    <h2>Recent Calculations</h2>
                    <p>Your recently calculated loans</p>
                </div>
            </div>

            @foreach($loans as $loan)
                <div class="loan-item">
                    <div class="loan-item-info">
                        <span><strong>${{ number_format($loan->loan_amount, 2) }}</strong> Principal</span>
                        <span><strong>{{ $loan->annual_interest_rate }}%</strong> Rate</span>
                        <span><strong>{{ $loan->loan_term_years }} years</strong> Term</span>
                        <span><strong>${{ number_format($loan->monthly_payment, 2) }}</strong> /month</span>
                        @if($loan->extra_monthly_payment > 0)
                            <span style="color: var(--success);"><strong>+${{ number_format($loan->extra_monthly_payment, 2) }}</strong> Extra</span>
                        @endif
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('loan.show', $loan) }}" class="btn btn-secondary btn-sm">View</a>
                        <form action="{{ route('loan.destroy', $loan) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this loan?')">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

