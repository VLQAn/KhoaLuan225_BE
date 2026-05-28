<?php

namespace App\Repositories\Interfaces;

interface BapNuocRepositoryInterface
{
    public function getAll();

    public function getByOwner($maNguoiDung);

    public function getById($id);

    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);

    public function find($id);
}