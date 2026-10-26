<?php

namespace App\Enums;

enum SoilType: string
{
    case Clay   = 'clay';
    case Sandy  = 'sandy';
    case Loamy  = 'loamy';
    case Silty  = 'silty';
    case Rocky  = 'rocky';
}
