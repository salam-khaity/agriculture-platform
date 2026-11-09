<?php

namespace App\Models;


use App\Enums\ActivityType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Farm extends Model
{
    /** @use HasFactory<\Database\Factories\FarmFactory> */

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'latitude',
        'longitude',
        'area',
        'location_description',
        'activity_type',
    ];

    protected function casts(): array
    {
        return [
            'latitude'  => 'float',
            'longitude' => 'float',
            'area'      => 'float',
            'activity_type' => ActivityType::class,
        ];
    }

    protected $attributes = ['activity_type' => 'plant'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function scopeOwnedBy(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class);
    }
}
