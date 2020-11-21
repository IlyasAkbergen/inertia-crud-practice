<?php


namespace App\Services;

use App\Models\Movie;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class MovieServiceImpl extends BaseServiceImpl implements MovieService
{
    public function __construct(Movie $model)
    {
        parent::__construct($model);
    }

    public function all()
    {
        return $this->model->orderByDesc('created_at')->get();
    }

    public function allWith(array $relationships)
    {
        return $this->model->with($relationships)
            ->orderByDesc('created_at')
            ->get();
    }

    public function addReview(array $attributes)
    {
        $attributes = array_merge($attributes, [
            'reviewable_type' => Movie::class,
            'user_id' => Auth::user()->id
        ]);

        return Review::create($attributes);
    }
}