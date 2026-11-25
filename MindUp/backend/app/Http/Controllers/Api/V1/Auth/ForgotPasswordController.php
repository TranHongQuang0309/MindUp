<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function __invoke(
        ForgotPasswordRequest $request
    ): JsonResponse {
        $data = $request->validated();

        Password::sendResetLink([
            'email' => $data['email'],
        ]);

        return response()->json([
            'message' => 'Nếu email thuộc một tài khoản trong hệ thống '
                .'và yêu cầu đủ điều kiện, bạn sẽ nhận được '
                .'liên kết đặt lại mật khẩu.',
        ]);
    }
}