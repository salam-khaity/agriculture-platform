<?php

namespace App\Enums;

enum ActivityType: string
{
    case Plant  = 'plant';
    case Animal = 'animal';
    case Mixed  = 'mixed';
}
