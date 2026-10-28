<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // role و status خارج fillable عمداً: لا يُسندان إلا بإسناد صريح من الـ Service
    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'banned_at'         => 'datetime',
            'role'              => UserRole::class,
            'status'            => UserStatus::class,
        ];
    }

    // ---------------------------------------------------------------
    // Role helpers
    // ---------------------------------------------------------------

    public function isActive(): bool
    {
        return $this->status === UserStatus::Active && $this->banned_at === null;
    }

    public function hasRole(UserRole ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    // مكان واحد يحدد من هو الأدمن (كان يقارن نصاً، والآن يقارن الـ Enum)
    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::Admin);
    }

    // ---------------------------------------------------------------
    // Relations: farming (our part)
    // ---------------------------------------------------------------

    public function farms(): HasMany
    {
        return $this->hasMany(Farm::class);
    }

    // ---------------------------------------------------------------
    // Relations: role profiles (colleague's part)
    // ---------------------------------------------------------------

    public function specialistProfile(): HasOne
    {
        return $this->hasOne(SpecialistProfile::class);
    }

    public function pharmacyStaffProfile(): HasOne
    {
        return $this->hasOne(PharmacyStaffProfile::class);
    }

    public function deliveryAgentProfile(): HasOne
    {
        return $this->hasOne(DeliveryAgentProfile::class);
    }

    public function labEmployeeProfile(): HasOne
    {
        return $this->hasOne(LabEmployeeProfile::class);
    }
}
