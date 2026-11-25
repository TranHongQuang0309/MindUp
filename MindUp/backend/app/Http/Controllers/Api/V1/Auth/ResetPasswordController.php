<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ResetPasswordController extends Controller
{
    public function __invoke(
        ResetPasswordRequest $request
    ): JsonResponse {
        $data = $request->validated();
        $resetUser = null;

        $status = DB::transaction(function () use (
            $data,
            &$resetUser
        ) {
            return Password::reset(
                $data,
                function (User $user, string $password) use (
                    &$resetUser
                ): void {
                    $user->password = $password;
                    $user->setRememberToken(Str::random(60));
                    $user->save();

                    DB::table('sessions')
                        ->where('user_id', $user->id)
                        ->delete();

                    $resetUser = $user;
                }
            );
        });

        if ($status !== Password::PasswordReset) {
            throw ValidationException::withMessages([
                'token' => 'Liên kết đặt lại mật khẩu không hợp lệ '
                    .'hoặc đã hết hạn. Vui lòng yêu cầu liên kết mới.',
            ]);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        event(new PasswordReset($resetUser));

        return response()->json([
            'message' => 'Đặt lại mật khẩu thành công. '
                .'Vui lòng đăng nhập bằng mật khẩu mới.',
        ]);
    }
}