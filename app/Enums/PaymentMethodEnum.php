<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentMethodEnum: string
{
    case MTN_MOMO = 'mtn_momo';
    case VODAFONE_CASH = 'vodafone_cash';
    case TELECEL_CASH = 'telecel_cash';
    case AIRTELTIGO_MONEY = 'airteltigo_money';
    case CARD = 'card';
    case USSD = 'ussd';
}

