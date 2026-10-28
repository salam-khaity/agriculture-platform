<?php

namespace App\Enums;

enum UserRole: string
{
    case Farmer                 = 'farmer';
    case AgriculturalSpecialist = 'agricultural_specialist';
    case PharmacyStaff          = 'pharmacy_staff';
    case DeliveryAgent          = 'delivery_agent';
    case LabEmployee            = 'lab_employee';
    case Admin                  = 'admin';

    /** الأدوار المسموح لها بالتسجيل العام — المصدر الوحيد للحقيقة */
    public static function publicRegistrable(): array
    {
        return [
            self::Farmer,
            self::AgriculturalSpecialist,
            self::DeliveryAgent,
        ];
    }

    public static function publicValues(): array
    {
        return array_map(fn (self $r) => $r->value, self::publicRegistrable());
    }

    public function isPublicRegistrable(): bool
    {
        return in_array($this, self::publicRegistrable(), true);
    }

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }

    /** المزارع يُفعَّل مباشرة بعد التحقق من الإيميل، الباقي بحاجة موافقة الإدارة */
    public function requiresApproval(): bool
    {
        return $this !== self::Farmer;
    }
}
