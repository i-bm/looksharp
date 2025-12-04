<?php

namespace App\Enums;

enum StatusEnum: string
{
    case PENDING = 'pending';
    case VERIFIED = 'verified';
    case REJECTED = 'rejected';
}
