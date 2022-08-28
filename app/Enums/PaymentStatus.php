<?php

namespace App\Enums;

enum PaymentStatus: string
{

    case PENDING = 'PENDING';
    case DUE = 'DUE';
    case PAID = 'PAID';
    case REPAID = 'REPAID';
    case PARTIAL = 'PARTIAL';

}
