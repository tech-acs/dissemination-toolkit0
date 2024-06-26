<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Year extends Model
{
    protected $guarded = ['id'];

    public function datasets(): BelongsToMany
    {
        return $this->belongsToMany(Dataset::class);
    }
}
