<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        if (User::query()->where('email', 'test@example.com')->exists()) {
            return;
        }

        User::factory()->create([
            'full_name' => 'Người dùng thử nghiệm',
            'email' => 'test@example.com',
        ]);
    }
}