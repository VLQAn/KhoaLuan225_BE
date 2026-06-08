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
            ->get();
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
        // TÁCH THỂ LOẠI
        $theLoaiIds = $data['theLoai'] ?? [];

        unset($data['theLoai']);

        // TẠO PHIM
        $movie = Phim::create($data);

        // SYNC THỂ LOẠI
        $movie->theLoai()->sync(
            $theLoaiIds
        );

        // LOAD LẠI RELATION
        return $movie->load('theLoai');
    }

    /**
     * Update movie
     */
    public function update(
        int $id,
        array $data
    ) {
        $movie = Phim::findOrFail($id);

        // TÁCH THỂ LOẠI
        $theLoaiIds = $data['theLoai'] ?? [];

        unset($data['theLoai']);

        // UPDATE PHIM
        $movie->update($data);

        // UPDATE THỂ LOẠI
        $movie->theLoai()->sync(
            $theLoaiIds
        );

        return $movie->load('theLoai');
    }

    /**
     * Delete movie
     */
    public function delete(int $id)
    {
        $movie = Phim::findOrFail($id);

        return $movie->delete();
    }

    /**
     * Find movies by year
     */
    public function findByYear(
        int $year
    ) {
        return Phim::with('theLoai')
            ->whereYear(
                'ngayCongChieu',
                $year
            )
            ->get();
    }
}
