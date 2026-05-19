<?php

namespace App\Repositories\Eloquent;

use App\Models\Phim;
use App\Repositories\Interfaces\MovieRepositoryInterface;

class MovieRepository
    implements MovieRepositoryInterface
{
    /**
     * Get all movies
     */
    public function getAll()
    {
        return Phim::with('theLoai')
            ->latest()
            ->paginate(10);
    }

    /**
     * Find by id
     */
    public function findById(int $id)
    {
        return Phim::with('theLoai')
            ->findOrFail($id);
    }

    /**
     * Create movie
     */
    public function create(array $data)
    {
        return Phim::create($data);
    }

    /**
     * Update movie
     */
    public function update(
        int $id,
        array $data
    ) {
        $movie = Phim::findOrFail($id);

        $movie->update($data);

        return $movie;
    }

    /**
     * Delete movie
     */
    public function delete(int $id)
    {
        $movie = Phim::findOrFail($id);

        return $movie->delete();
    }
}
