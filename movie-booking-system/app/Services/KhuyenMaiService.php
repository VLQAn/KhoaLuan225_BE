<?php

namespace App\Services;

use Exception;
use App\Models\RapChieu;

use App\Repositories\Interfaces\KhuyenMaiRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class KhuyenMaiService
{
    protected $khuyenMaiRepository;

    public function __construct(
        KhuyenMaiRepositoryInterface
        $khuyenMaiRepository
    ) {
        $this->khuyenMaiRepository
            = $khuyenMaiRepository;
    }

    public function getAllKhuyenMai()
    {
        return $this->khuyenMaiRepository
            ->query()
            ->where('maNguoiDung', Auth::id())
            ->get();
    }

    public function getKhuyenMaiById($id)
    {
        return $this->khuyenMaiRepository
            ->findById($id);
    }

    public function createKhuyenMai(array $data)
    {
        $data['maNguoiDung'] = Auth::id();

        return $this->khuyenMaiRepository->create($data);
    }

    public function updateKhuyenMai($id, array $data)
    {
        $km = $this->khuyenMaiRepository->findById($id);

        if ($km->maNguoiDung !== Auth::id()) {
            throw new Exception("Không có quyền");
        }

        return $this->khuyenMaiRepository->update($id, $data);
    }

    public function deleteKhuyenMai($id)
    {
        $km = $this->khuyenMaiRepository->findById($id);

        if ($km->maNguoiDung !== Auth::id()) {
            throw new Exception("Không có quyền");
        }

        return $this->khuyenMaiRepository->delete($id);
    }

    public function getPublicKhuyenMai()
    {
        return $this->khuyenMaiRepository
            ->query()
            ->where(
                'thoiHan',
                '>=',
                now()
            )
            ->get();
    }

    public function getMyKhuyenMai()
    {
        return $this->khuyenMaiRepository
            ->query()
            ->where(
                'maNguoiDung',
                Auth::id()
            )
            ->get();
    }
}
