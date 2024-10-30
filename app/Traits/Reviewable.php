<?php

namespace App\Traits;

use App\Models\Review;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Reviewable
{
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function getRatingAttribute()
    {
        $avgRating = $this->reviews()->approved()->avg('rating');
        return is_null($avgRating) ? '-' : (int) $avgRating;
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviews()->approved()->count();
    }
}
