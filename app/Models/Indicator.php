<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function datasets(): HasMany
    {
        return $this->hasMany(Dataset::class);
    }
}
