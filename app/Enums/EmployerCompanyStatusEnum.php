<?php

declare(strict_types=1);

namespace App\Enums;

enum EmployerCompanyStatusEnum: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case APPROVED = 'approved';
    case NEEDS_CHANGES = 'needs_changes';
    case REJECTED = 'rejected';
    case SUSPENDED = 'suspended';
}

