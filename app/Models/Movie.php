<?php

namespace App\Models;

use App\Events\MovieCreated;
use App\Events\MovieUpdated;
use App\Events\MovieDeleted;
use App\Models\Interfaces\Reviewable;
use App\Models\Traits\HasReviews;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model implements Reviewable
{
    use HasFactory;
    use HasReviews;

    protected $fillable = [
        'title', 'description', 'reviews_count', 'average_stars'
    ];

    protected $dispatchesEvents = [
        'created' => MovieCreated::class,
        'updated' => MovieUpdated::class,
        'deleted' => MovieDeleted::class
    ];
}
