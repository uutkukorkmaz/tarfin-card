<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CurrencyType;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{

    use HasFactory;

    use HasPaymentStatus;

    // region Attributes

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'amount',
        'outstanding_amount',
        'currency_code',
        'terms',
        'status',
        'processed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => PaymentStatus::class,
        'currency_code' => CurrencyType::class,
        'processed_at' => 'datetime',
    ];

    // endregion

    // region Relations
    /**
     * A Loan belongs to a User.
     *
     * @return BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A Loan has many Scheduled Repayments.
     *
     * @return HasMany<ScheduledRepayment>
     */
    public function scheduledRepayments(): HasMany
    {
        return $this->hasMany(ScheduledRepayment::class);
    }

    /**
     * A Loan has many Repayments.
     *
     * @return HasMany<ReceivedRepayment>
     */
    public function receivedRepayments(): HasMany
    {
        return $this->hasMany(ReceivedRepayment::class);
    }

    // endregion
}
