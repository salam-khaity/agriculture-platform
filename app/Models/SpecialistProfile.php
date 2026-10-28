<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SpecialistProfile extends Model
{
    protected $fillable = [
        'user_id',
        'specialization_id',
        'bio',
        'experience_years',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }

    public function productionTypes(): BelongsToMany
    {
        return $this->belongsToMany(ProductionType::class, 'specialist_production_type', 'specialist_profile_id', 'production_type_id');
    }

    public function regions(): BelongsToMany
    {
        return $this->belongsToMany(Region::class, 'specialist_region', 'specialist_profile_id', 'region_id');
    }
}
