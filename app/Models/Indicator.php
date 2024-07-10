<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class Indicator extends Model
{
    use HasTranslations;

    protected $guarded = ['id'];
    public array $translatable = ['name', 'description'];
    protected $casts = [
        'data' => 'array',
        'layout' => 'array'
    ];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function datasets(): BelongsToMany
    {
        return $this->belongsToMany(Dataset::class);
    }

    public function getSlugAttribute(): string
    {
        return str($this->name)->slug()->value();
    }
}
