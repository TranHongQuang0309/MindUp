<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $request->user()->only([
                'id',
                'full_name',
                'email',
                'role',
                'status',
                'email_verified_at',
            ]),
        ]);
    }
}