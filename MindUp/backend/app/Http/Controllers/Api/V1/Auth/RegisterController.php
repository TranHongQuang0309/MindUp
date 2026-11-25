<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = new User();
        $user->full_name = $data['full_name'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->role = 'user';
        $user->status = 'active';
        $user->save();

        return response()->json([
            'message' => 'Đăng ký tài khoản thành công.',
            'data' => $user->only([
                'id',
                'full_name',
                'email',
                'role',
                'status',
                'email_verified_at',
            ]),
        ], 201);
    }
}