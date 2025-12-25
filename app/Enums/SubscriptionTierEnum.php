<?php

declare(strict_types=1);

namespace App\Enums;

enum SubscriptionTierEnum: string
{
    case FREE = 'free';
    case STARTER = 'starter';
    case PROFESSIONAL = 'professional';
}

