<?php

namespace App\Enums;

enum DegreeTypeEnum: string
{
    case CERTIFICATE = 'certificate';
    case DIPLOMA = 'diploma';
    case BACHELORS = 'bachelors';
    case MASTERS = 'masters';
    case PHD = 'phd';
}
