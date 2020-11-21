<?php

namespace App\Http\Controllers;

use App\Services\MovieService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    private $movieService;

    public function __construct(MovieService $movieService)
    {
        $this->movieService = $movieService;
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'note' => ['required'],
            'reviewable_id' => ['required', 'exists:App\Models\Movie,id'],
            'stars' => ['required', 'numeric', 'min:0', 'max:5']
        ])->validate();

        $this->movieService->addReview($request->only([
            'note', 'reviewable_id', 'stars'
        ]));

        return redirect()->back()
            ->with('message', 'Review Added Successfully.');
    }
}
