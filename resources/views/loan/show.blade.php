@extends('layouts.app')

@section('title', 'Loan Schedule')

@section('content')
    <!-- Loan Header with Details (Required by Requirement #7) -->
    <div class="loan-header">
        <h2>📋 Loan Setup Details</h2>
        <div class="loan-header-grid">
            <div class="loan-header-item">
                <div class="label">Loan Amount</div>
                <div class="value">${{ number_format($loan->loan_amount, 2) }}</div>
            </div>
            <div class="loan-header-item">
                <div class="label">Annual Interest Rate</div>
                <div class="value">{{ number_format($loan->annual_interest_rate, 2) }}%</div>
            </div>
            <div class="loan-header-item">
                <div class="label">Loan Term</div>
                <div class="value">{{ $loan->loan_term_years }} Years</div>
            </div>
            <div class="loan-header-item">
                <div class="label">Monthly Payment</div>
                <div class="value">${{ number_format($loan->monthly_payment, 2) }}</div>
            </div>
            <div class="loan-header-item">
                <div class="label">Effective Interest Rate</div>
                <div class="value">{{ number_format($loan->effective_interest_rate, 2) }}%</div>
            </div>
            @if($loan->extra_monthly_payment > 0)
                <div class="loan-header-item">
                    <div class="label">Extra Monthly Payment</div>
                    <div class="value">${{ number_format($loan->extra_monthly_payment, 2) }}</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Schedule Tables -->
    <div class="card">
        <div class="tabs">
            <button class="tab active" onclick="showTab('standard')">📅 Standard Amortization</button>
            @if($loan->extraRepaymentSchedule->count() > 0)
                <button class="tab" onclick="showTab('extra')">💨 With Extra Payments</button>
            @endif
        </div>

        <!-- Standard Amortization Schedule -->
        <div id="standard-tab" class="tab-content active">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Starting Balance</th>
                            <th>Monthly Payment</th>
                            <th>Principal</th>
                            <th>Interest</th>
                            <th>Ending Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loan->amortizationSchedule as $entry)
                            <tr>
                                <td>{{ $entry->month_number }}</td>
                                <td class="money">${{ number_format($entry->starting_balance, 2) }}</td>
                                <td class="money">${{ number_format($entry->monthly_payment, 2) }}</td>
                                <td class="money positive">${{ number_format($entry->principal_component, 2) }}</td>
                                <td class="money negative">${{ number_format($entry->interest_component, 2) }}</td>
                                <td class="money">${{ number_format($entry->ending_balance, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Extra Repayment Schedule -->
        @if($loan->extraRepaymentSchedule->count() > 0)
            <div id="extra-tab" class="tab-content">
                <!-- Header for Extra Repayment Schedule (Required by Requirement #7) -->
                <div class="loan-header" style="background: linear-gradient(135deg, var(--success), #059669); margin-bottom: 1.5rem;">
                    <h2>📋 Recalculated Loan Details (With Extra Payments)</h2>
                    <div class="loan-header-grid">
                        <div class="loan-header-item">
                            <div class="label">Loan Amount</div>
                            <div class="value">${{ number_format($loan->loan_amount, 2) }}</div>
                        </div>
                        <div class="loan-header-item">
                            <div class="label">Annual Interest Rate</div>
                            <div class="value">{{ number_format($loan->annual_interest_rate, 2) }}%</div>
                        </div>
                        <div class="loan-header-item">
                            <div class="label">Extra Monthly Payment</div>
                            <div class="value">${{ number_format($loan->extra_monthly_payment, 2) }}</div>
                        </div>
                        <div class="loan-header-item">
                            <div class="label">Actual Loan Duration</div>
                            <div class="value">{{ $loan->extraRepaymentSchedule->count() }} Months</div>
                        </div>
                        <div class="loan-header-item">
                            <div class="label">Effective Interest Rate</div>
                            <div class="value">{{ number_format($loan->effective_interest_rate, 2) }}%</div>
                        </div>
                    </div>
                </div>
                
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Starting Balance</th>
                                <th>Monthly Payment</th>
                                <th>Principal</th>
                                <th>Interest</th>
                                <th>Extra Payment</th>
                                <th>Ending Balance</th>
                                <th>Remaining Term</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loan->extraRepaymentSchedule as $entry)
                                <tr>
                                    <td>{{ $entry->month_number }}</td>
                                    <td class="money">${{ number_format($entry->starting_balance, 2) }}</td>
                                    <td class="money">${{ number_format($entry->monthly_payment, 2) }}</td>
                                    <td class="money positive">${{ number_format($entry->principal_component, 2) }}</td>
                                    <td class="money negative">${{ number_format($entry->interest_component, 2) }}</td>
                                    <td class="money positive">@if($entry->extra_repayment_made > 0)+${{ number_format($entry->extra_repayment_made, 2) }}@else-@endif</td>
                                    <td class="money">${{ number_format($entry->ending_balance_after_extra, 2) }}</td>
                                    <td>{{ $entry->remaining_loan_term }} months</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <div style="margin-top: 2rem; display: flex; gap: 1rem;">
        <a href="{{ route('loan.index') }}" class="btn btn-primary">← Calculate New Loan</a>
        <form action="{{ route('loan.destroy', $loan) }}" method="POST" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this loan?')">🗑️ Delete This Loan</button>
        </form>
    </div>
@endsection

@section('scripts')
<script>
    function showTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
        document.getElementById(tabName + '-tab').classList.add('active');
        event.target.classList.add('active');
    }
</script>
@endsection

