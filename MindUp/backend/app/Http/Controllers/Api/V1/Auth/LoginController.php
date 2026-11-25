<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $authenticated = Auth::guard('web')->attempt([
            'email' => $data['email'],
            'password' => $data['password'],
            'status' => 'active',
        ]);

        if (! $authenticated) {
            throw ValidationException::withMessages([
                'email' => 'Không thể đăng nhập. Vui lòng kiểm tra '
                    .'thông tin đăng nhập hoặc trạng thái tài khoản.',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::guard('web')->user();

        return response()->json([
            'message' => 'Đăng nhập thành công.',
            'data' => $user->only([
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