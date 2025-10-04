<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ContactService;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    protected ContactService $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function index(): JsonResponse
    {
        $contact = $this->contactService->getPrimaryContact();

        if (!$contact) {
            return response()->json([
                'success' => false,
                'message' => 'Contact information not available',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $contact,
        ]);
    }
}
