<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->decimal('loan_amount', 15, 2);
            $table->decimal('annual_interest_rate', 5, 2);
            $table->integer('loan_term_years');
            $table->decimal('extra_monthly_payment', 15, 2)->nullable()->default(0);
            $table->decimal('monthly_payment', 15, 2);
            $table->decimal('effective_interest_rate', 8, 4)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};

