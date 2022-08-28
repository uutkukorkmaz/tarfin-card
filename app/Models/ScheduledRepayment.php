<?php

namespace App\Models;

use App\Enums\CurrencyType;
use App\Enums\PaymentStatus;
use App\Traits\HasPaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledRepayment extends Model
{

    use HasFactory;
    use HasPaymentStatus;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'loan_id',
        'amount',
        'outstanding_amount',
        'currency_code',
        'due_date',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => PaymentStatus::class,
        'currency_code' => CurrencyType::class,
        'due_date' => 'immutable_date',
    ];

}
