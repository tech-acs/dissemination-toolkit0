<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Area extends Model
{
    use HasTranslations;

    protected $guarded = ['id'];
    public $translatable = ['name'];

    public function scopeOfLevel(Builder $query, $level)
    {
        return $query->where('level', $level);
    }
}
