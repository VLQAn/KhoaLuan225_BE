<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Services\TheLoaiService;
use App\Http\Resources\TheLoaiResource;

class TheLoaiController extends Controller
{
    protected $theLoaiService;

    public function __construct(
        TheLoaiService $theLoaiService
    ) {
        $this->theLoaiService = $theLoaiService;
    }

    /**
     * Get all genres
     */
    public function index()
    {
        $genres = $this->theLoaiService
            ->getAllTheLoai();

        return ApiResponse::success(
            TheLoaiResource::collection($genres),
            'Genres fetched successfully'
        );
    }
}
