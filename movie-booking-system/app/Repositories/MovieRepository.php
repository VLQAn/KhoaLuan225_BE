<?php

namespace App\Repositories;

use App\Models\Movie;
use App\Interfaces\MovieRepositoryInterface;

class MovieRepository implements MovieRepositoryInterface
{
    public function getAll()
    {
        return Movie::with('genres')
            ->latest()
            ->paginate(10);
    }

    public function findById(int $id)
    {
        return Movie::with('genres')
            ->findOrFail($id);
    }

    public function create(array $data)
    {
        return Movie::create($data);
    }

    public function update(int $id, array $data)
    {
        $movie = Movie::findOrFail($id);

        $movie->update($data);

        return $movie;
    }

    public function delete(int $id)
    {
        $movie = Movie::findOrFail($id);

        return $movie->delete();
    }
}