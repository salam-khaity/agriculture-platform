<?php

namespace App\Enums;

enum ProductionStatus: string
{
    case Active    = 'active';
    case Harvested = 'harvested';
    case Failed    = 'failed';
}
