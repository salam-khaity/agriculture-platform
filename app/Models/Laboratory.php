<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Laboratory extends Model
{
    protected $table = 'laboratories';

    use HasFactory;

    protected $fillable = [
        'name',
        'address_id',
        'manager_name',
        'email',
        'description',
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
