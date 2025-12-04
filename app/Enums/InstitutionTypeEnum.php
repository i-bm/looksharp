<?php

namespace App\Enums;

enum InstitutionTypeEnum: string
{
    case UNIVERSITY = 'university';
    case POLYTECHNIC = 'polytechnic';
    case COLLEGE = 'college';
    case OTHER = 'other';
}
