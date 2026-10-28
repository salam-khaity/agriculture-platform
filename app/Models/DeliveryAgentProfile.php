<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DeliveryAgentProfile extends Model
{
    protected $fillable = [
        'user_id',
        'vehicle_type',
        'vehicle_number',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function regions(): BelongsToMany
    {
        return $this->belongsToMany(
            Region::class,
            'delivery_agent_region',
            'delivery_agent_profile_id',
            'region_id'
        );
    }
}
