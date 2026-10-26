<?php

namespace App\Enums;

enum CropCategory: string
{
    case Cereal     = 'cereal';
    case Vegetable  = 'vegetable';
    case Fruit      = 'fruit';
    case Legume     = 'legume';
    case Tree       = 'tree';
    case Industrial = 'industrial';
}
