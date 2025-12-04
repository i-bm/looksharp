<?php

namespace App\Enums;

enum UserRoleEnum: string
{
    case ADMIN = 'admin';
    case EMPLOYER = 'employer';
    case TALENT = 'talent';
    case UNIVERSITY = 'university';
    case NATIONAL_SERVICE = 'national_service';
    case OTHER = 'other';
}
