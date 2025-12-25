<?php

declare(strict_types=1);

namespace App\Enums;

enum BillingCycleEnum: string
{
    case MONTHLY = 'monthly';
    case ANNUAL = 'annual';
}

