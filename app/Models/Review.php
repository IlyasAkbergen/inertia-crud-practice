<?php

namespace App\Models;

use App\Events\ReviewCreated;
use App\Events\ReviewDeleted;
use App\Events\ReviewUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    static $MORPH_NAME = 'reviewable';

    protected $fillable = [
        'note', 'user_id', 'reviewable_id', 'reviewable_type', 'stars'
    ];

    public function reviewable()
    {
        return $this->morphTo();
    }

    protected $dispatchesEvents = [
        'created' => ReviewCreated::class,
        'updated' => ReviewUpdated::class,
        'deleted' => ReviewDeleted::class,
    ];
}
