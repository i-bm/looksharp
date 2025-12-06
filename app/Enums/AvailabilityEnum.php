<?php

namespace App\Enums;

enum AvailabilityEnum: string
{
    case FULL_TIME = 'full_time';
    case PART_TIME = 'part_time';
    case INTERNSHIP = 'internship';
    case CONTRACT = 'contract';
    case FLEXIBLE = 'flexible';
}

