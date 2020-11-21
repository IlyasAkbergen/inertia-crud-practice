<?php


namespace App\Services;


use App\Models\Review;

interface MovieService
{
    /**
     * @param array $attributes
     * @return Review
     */
    public function addReview(array $attributes);
}