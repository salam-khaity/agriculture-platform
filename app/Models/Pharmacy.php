<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pharmacy extends Model
{
    protected $table = 'pharmacies';

    use HasFactory;

    protected $fillable = [
        'name',
        'owner_name',
        'email',
        'description',
        'address_id',
        'phone',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
