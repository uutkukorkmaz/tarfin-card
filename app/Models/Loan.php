<?php

namespace App\Models;

use App\Enums\CurrencyType;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{

    use HasFactory;

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
     * */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // endregion

}
