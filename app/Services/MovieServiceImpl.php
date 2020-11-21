<?php


namespace App\Services;

use App\Models\Movie;

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
}