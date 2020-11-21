<?php

namespace App\Http\Controllers;

use App\Services\MovieService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class MovieController extends Controller
{
    private $movieService;

    public function __construct(MovieService $movieService)
    {
        $this->movieService = $movieService;
    }

    public function index()
    {
        $movies = $this->movieService->allWith(['reviews']);
        return Inertia::render('Movies', ['movies' => $movies]);
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'title' => ['required']
        ])->validate();

        $this->movieService->create($request->only(['title', 'description']));

        return redirect()->back()
            ->with('message', 'Movie Created Successfully.');
    }

    public function update(Request $request)
    {
        Validator::make($request->all(), [
            'id' => ['required'],
            'title' => ['required']
        ])->validate();

        $this->movieService->update(
            $request->input('id'),
            $request->only([
                'title',
                'description'
            ])
        );

        return redirect()->back()
            ->with('message', 'Movie Updated Successfully.');
    }

    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            $this->movieService->delete($request->input('id'));
        }

        return redirect()->back();
    }
}
