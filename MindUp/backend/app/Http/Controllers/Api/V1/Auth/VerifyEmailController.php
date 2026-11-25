<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;

class VerifyEmailController extends Controller
{
    public function __invoke(
        EmailVerificationRequest $request
    ): JsonResponse {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email đã được xác minh trước đó.',
            ]);
        }

        $request->fulfill();

        return response()->json([
            'message' => 'Xác minh email thành công.',
        ]);
    }
}