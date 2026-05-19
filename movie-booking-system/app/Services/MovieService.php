<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Interfaces\MovieRepositoryInterface;

class MovieService
{
    protected $movieRepository;

    public function __construct(
        MovieRepositoryInterface $movieRepository
    ) {
        $this->movieRepository = $movieRepository;
    }

    /**
     * Get all movies
     */
    public function getAllMovies()
    {
        return $this->movieRepository->getAll();
    }

    /**
     * Get movie detail
     */
    public function getMovieDetail(int $id)
    {
        return $this->movieRepository->findById($id);
    }

    /**
     * Create movie
     */
    public function createMovie(array $data)
    {
        $data['slug'] = Str::slug($data['title']);

        return $this->movieRepository->create($data);
    }

    /**
     * Update movie
     */
    public function updateMovie(
        int $id,
        array $data
    ) {
        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        return $this->movieRepository->update(
            $id,
            $data
        );
    }

    /**
     * Delete movie
     */
    public function deleteMovie(int $id)
    {
        return $this->movieRepository->delete($id);
    }
}