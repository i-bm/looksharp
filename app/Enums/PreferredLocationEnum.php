<?php

namespace App\Enums;

enum PreferredLocationEnum: string
{
    case REMOTE = 'remote';
    case HYBRID = 'hybrid';
    case ON_SITE = 'on_site';
}

