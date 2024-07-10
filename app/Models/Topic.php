<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Topic extends Model
{
    use HasTranslations;

    protected $guarded = ['id'];
    public array $translatable = ['name', 'description'];

    public function datasets(): HasMany
    {
        return $this->hasMany(Dataset::class);
    }

    public function indicators(): HasMany
    {
        return $this->hasMany(Indicator::class);
    }

    public function visualizations(): HasMany
    {
        return $this->hasMany(Visualization::class);
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class);
    }

    public function censusTables(): BelongsToMany
    {
        return $this->belongsToMany(CensusTable::class);
    }
}
