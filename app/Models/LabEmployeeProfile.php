<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabEmployeeProfile extends Model
{
    protected $fillable = ['user_id', 'laboratory_id', 'job_title'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function laboratory(): BelongsTo
    {
        return $this->belongsTo(Laboratory::class);
    }
}
