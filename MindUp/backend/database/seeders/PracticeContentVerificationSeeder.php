<?php

namespace Database\Seeders;

use App\Models\PracticeContent;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class PracticeContentVerificationSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException(
                'Chỉ tạo dữ liệu kiểm tra trong môi trường local hoặc testing.'
            );
        }

        // Thay 11 nếu tài khoản đang kiểm thử có ID khác.
        $owner = User::query()->findOrFail(11);

        if (
            $owner->status !== 'active'
            || ! $owner->hasVerifiedEmail()
        ) {
            throw new RuntimeException(
                'Tài khoản kiểm thử phải hoạt động và đã xác minh email.'
            );
        }

        $otherUser = User::query()
            ->where('id', '<>', $owner->id)
            ->orderBy('id')
            ->first();

        if (! $otherUser) {
            throw new RuntimeException(
                'Cần có ít nhất hai tài khoản để kiểm tra quyền xem nội dung.'
            );
        }

        $items = [
            [
                'owner' => $owner,
                'title' => '[TEST] Cá nhân của tôi',
                'scope' => 'private',
                'status' => 'available',
            ],
            [
                'owner' => $otherUser,
                'title' => '[TEST] Nội dung dùng chung',
                'scope' => 'shared',
                'status' => 'available',
            ],
            [
                'owner' => $otherUser,
                'title' => '[TEST] Cá nhân người khác',
                'scope' => 'private',
                'status' => 'available',
            ],
            [
                'owner' => $otherUser,
                'title' => '[TEST] Dùng chung ngừng cung cấp',
                'scope' => 'shared',
                'status' => 'unavailable',
            ],
            [
                'owner' => $owner,
                'title' => '[TEST] Cá nhân ngừng sử dụng',
                'scope' => 'private',
                'status' => 'unavailable',
            ],
        ];

        foreach ($items as $item) {
            $content = PracticeContent::query()
                ->where('created_by', $item['owner']->id)
                ->where('title', $item['title'])
                ->first();

            if (! $content) {
                $content = new PracticeContent();
            }

            $content->creator()->associate($item['owner']);
            $content->title = $item['title'];
            $content->description =
                'Nội dung mẫu phục vụ kiểm tra API danh sách.';
            $content->guidance =
                'Xem xét lợi ích, hạn chế và các điều kiện liên quan.';
            $content->scope = $item['scope'];
            $content->status = $item['status'];
            $content->save();
        }

        $this->command->info(
            'Đã chuẩn bị 5 nội dung kiểm tra cho tài khoản ID '
            .$owner->id.'.'
        );
    }
}