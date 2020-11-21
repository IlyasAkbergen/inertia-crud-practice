<?php

namespace App\Listeners;

use App\Events\ReviewCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class MovieEventsSubscriber implements ShouldQueue
{
    public function handleNewReview($event)
    {
        $review = $event->review;

        $review->load('reviewable.reviews');

        $reviewable = $review->reviewable;

        $reviews_count = count($reviewable->reviews);

        $review->reviewable->update([
           'average_stars' => $reviews_count == 0
               ?:$reviewable->reviews->sum('stars') / $reviews_count,
            'reviews_count' => $reviews_count
        ]);
    }

    public function subscribe($events)
    {
        $events->listen(
            ReviewCreated::class,
            [self::class, 'handleNewReview']
        );
    }
}
