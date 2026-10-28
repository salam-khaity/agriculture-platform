<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    protected $table = 'regions';

    protected $fillable = ['name', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
