<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Services\MovieService;
use App\Http\Controllers\Controller;
use App\Http\Resources\MovieResource;
use App\Http\Requests\Movie\StoreMovieRequest;
use App\Http\Requests\Movie\UpdateMovieRequest;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    protected $movieService;

    public function __construct(
        MovieService $movieService
    ) {
        $this->movieService = $movieService;
    }

    /**
     * Get all movies
     */
    public function index()
    {
        $movies = $this->movieService
            ->getAllMovies();

        return ApiResponse::success(
            MovieResource::collection($movies),
            'Movies fetched successfully'
        );
    }

    /**
     * Get movie detail
     */
    public function show(int $id)
    {
        $movie = $this->movieService
            ->getMovieDetail($id);

        return ApiResponse::success(
            new MovieResource($movie),
            'Movie detail fetched successfully'
        );
    }

    /**
     * Create movie
     */
    public function store(
        StoreMovieRequest $request
    ) {
        $movie = $this->movieService
            ->createMovie(
                $request->validated()
            );

        return ApiResponse::success(
            new MovieResource($movie),
            'Movie created successfully',
            201
        );
    }

    /**
     * Update movie
     */
    public function update(
        UpdateMovieRequest $request,
        int $id
    ) {
        $movie = $this->movieService
            ->updateMovie(
                $id,
                $request->validated()
            );

        return ApiResponse::success(
            new MovieResource($movie),
            'Movie updated successfully'
        );
    }

    /**
     * Delete movie
     */
    public function destroy(int $id)
    {
        $this->movieService
            ->deleteMovie($id);

        return ApiResponse::success(
            null,
            'Movie deleted successfully'
        );
    }

    /**
     * Change movie status
     */
    public function changeStatus(Request $request, int $id)
    {
        $movie = $this->movieService
            ->changeStatusMovie(
                $id,
                $request->trangThai
            );

        return ApiResponse::success(
            new MovieResource($movie),
            'Movie status updated successfully'
        );
    }
}
