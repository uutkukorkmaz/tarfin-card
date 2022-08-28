<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\PaymentStatus;
use Illuminate\Database\Query\Builder;

trait HasPaymentStatus
{

    public function scopeStatus(Builder $builder, PaymentStatus $status)
    {
        return $builder->where('status', $status);
    }

    public function scopeDue(Builder $builder)
    {
        return $builder->status(PaymentStatus::DUE);
    }

    public function scopePaid(Builder $builder)
    {
        return $builder->status(PaymentStatus::PAID);
    }

    public function scopeRepaid(Builder $builder)
    {
        return $builder->status(PaymentStatus::REPAID);
    }

    public function scopePartial(Builder $builder)
    {
        return $builder->status(PaymentStatus::PARTIAL);
    }

}
