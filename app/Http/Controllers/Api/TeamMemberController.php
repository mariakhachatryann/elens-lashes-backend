<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TeamMemberService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamMemberController extends Controller
{
    protected TeamMemberService $teamMemberService;

    public function __construct(TeamMemberService $teamMemberService)
    {
        $this->teamMemberService = $teamMemberService;
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 50);
        $members = $this->teamMemberService->getAll($perPage);

        return response()->json([
            'success' => true,
            'data' => $members,
        ]);
    }
}

