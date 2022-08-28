<?php

namespace App\Enums;

enum PaymentStatus: string
{

    case DUE = 'DUE';
    case REPAID = 'REPAID';
    case PARTIAL = 'PARTIAL';

}
