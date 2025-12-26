<?php

declare(strict_types=1);

namespace App\Enums;

enum EmployerCompanyMemberRoleEnum: string
{
    case COMPANY_ADMIN = 'company_admin';
    case RECRUITER = 'recruiter';
    case VIEWER = 'viewer';
}

