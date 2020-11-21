<?php


namespace App\Models\Traits;


use App\Models\Review;

trait HasReviews
{
    public function reviews()
    {
        return $this->morphMany(Review::class, Review::$MORPH_NAME);
    }
}