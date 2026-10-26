<?php

namespace App\Models\Concerns;

use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasMedia
{
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order')->orderBy('id');
    }

    // كل موديل يملك وسائط يحدد من هو صاحبه لتعمل الـ Policy بشكل عام
    abstract public function mediaOwnerId(): int;
}
