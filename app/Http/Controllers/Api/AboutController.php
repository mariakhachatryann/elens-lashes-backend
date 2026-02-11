<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AboutService;
use Illuminate\Http\JsonResponse;

class AboutController extends Controller
{
    protected AboutService $aboutService;

    public function __construct(AboutService $aboutService)
    {
        $this->aboutService = $aboutService;
    }

    public function index(): JsonResponse
    {
        $about = $this->aboutService->getAbout();

        if (!$about) {
            return response()->json([
                'success' => false,
                'message' => 'About information not available',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $about,
        ]);
    }
}

