<?php

namespace App\Services;

use App\Repositories\Interfaces\MovieRepositoryInterface;

class MovieService
{
    protected $movieRepository;

    public function __construct(
        MovieRepositoryInterface $movieRepository
    ) {
        $this->movieRepository =
            $movieRepository;
    }

    /**
     * Get all movies
     */
    public function getAllMovies()
    {
        return $this->movieRepository
            ->getAll();
    }

    /**
     * Get movie detail
     */
    public function getMovieDetail(
        int $id
    ) {
        return $this->movieRepository
            ->findById($id);
    }

    /**
     * Create movie
     */
    public function createMovie(
        array $data
    ) {
        return $this->movieRepository
            ->create($data);
    }

    /**
     * Update movie
     */
    public function updateMovie(
        int $id,
        array $data
    ) {
        return $this->movieRepository
            ->update($id, $data);
    }

    /**
     * Delete movie
     */
    public function deleteMovie(
        int $id
    ) {
        return $this->movieRepository
            ->delete($id);
    }

    public function changeStatusMovie(
        int $id,
        string $status
    ) {
        $movie = $this->movieRepository
            ->findById($id);

        $movie->trangThai = $status;

        $movie->save();

        return $movie;
    }
}
