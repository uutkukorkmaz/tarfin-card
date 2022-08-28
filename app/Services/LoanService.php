<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CurrencyType;
use App\Enums\PaymentStatus;
use App\Exceptions\AlreadyRepaidException;
use App\Exceptions\AmountHigherThanOutstandingAmountException;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LoanService
{

    /**
     * @param  string|\Carbon\Carbon|null  $processedAt
     */
    public function createLoan(
        User $user,
        int $amount,
        CurrencyType $currencyType,
        int $terms,
        Carbon $processedAt
    ): Loan {
        $loan = Loan::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'outstanding_amount' => $amount,
            'currency_code' => $currencyType->value,
            'terms' => $terms,
            'processed_at' => $processedAt ?? now(),
        ]);

        [$payment, $leftover] = $this->calculateScheduledRepayment($loan);

        for ($i = 1; $i <= $terms; $i++) {
            $scheduledAmount = $payment + ($i == $terms ? $leftover : 0);
            $loan->scheduledRepayments()->create([
                'amount' => $scheduledAmount,
                'outstanding_amount' => $scheduledAmount,
                'currency_code' => $currencyType->value,
                'due_date' => $loan->processed_at->clone()->addMonths($i),
            ]);
        }

        return $loan->load('scheduledRepayments');
    }

    public function calculateScheduledRepayment(Loan $loan): array
    {
        $payment = intval($loan->amount / $loan->terms);

        return [$payment, $loan->amount - ($payment * $loan->terms)];
    }

    /**
     * @throws \App\Exceptions\AlreadyRepaidException
     * @throws \App\Exceptions\AmountHigherThanOutstandingAmountException
     */
    public function repayLoan(Loan $loan, int $amount, CurrencyType $currency, Carbon $received_at): Loan
    {
        // initial checks
        if ($loan->status === PaymentStatus::REPAID) {
            throw new AlreadyRepaidException();
        }
        if ($amount > $loan->amount) {
            throw new AmountHigherThanOutstandingAmountException();
        }

        // begin database transaction
        DB::beginTransaction();

        // store the repayment
        $received = $loan->receivedRepayments()->create([
            'amount' => $amount,
            'currency_code' => $currency->value,
            'received_at' => $received_at,
        ]);

        // update the scheduled repayments
        $this->payScheduledRepayment($loan, $amount);

        // update the loan
        $outstanding = $loan->outstanding_amount - $amount;
        $loan->update([
            'outstanding_amount' => $outstanding,
            'status' => $outstanding == 0 ? PaymentStatus::REPAID : PaymentStatus::DUE,
        ]);

        // commit all the changes to the database and end the transaction
        DB::commit();

        return $loan;
    }

    protected function payScheduledRepayment(Loan $loan, int $received_amount): void
    {
        // get closest unpaid repayment
        $scheduledPayment = $loan->scheduledRepayments()
            ->where('outstanding_amount', '>', 0)
            ->orderBy('due_date')
            ->first();

        // if that exists
        if ($scheduledPayment) {
            // calculate if there is any leftover
            $leftover = $scheduledPayment->outstanding_amount - $received_amount;
            // update the repayment
            $scheduledPayment->update([
                'outstanding_amount' => max($leftover, 0),
                'status' => $this->detectStatus($received_amount, max($leftover, 0)),
            ]);
            // if there is any leftover, pay it
            if ($leftover < 0) {
                $this->payScheduledRepayment($loan, intval(abs($leftover)));
            }
        }
    }

    /**
     * @param $amount
     * @param $outstandingAmount
     */
    protected function detectStatus($amount, $outstandingAmount): PaymentStatus
    {
        return match (true) {
            min($outstandingAmount, $amount) == 0 => PaymentStatus::REPAID,
            $outstandingAmount == $amount => PaymentStatus::DUE,
            default => PaymentStatus::PARTIAL,
        };
    }

}
