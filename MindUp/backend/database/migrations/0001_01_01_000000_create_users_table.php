<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tạo bảng tài khoản, mã đặt lại mật khẩu và phiên truy cập.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('full_name', 100);
            $table->string('email', 255)->unique();
            $table->timestampTz('email_verified_at')->nullable();
            $table->string('password', 255);

            $table->string('role', 20)->default('user');
            $table->string('status', 20)->default('active');

            $table->rememberToken();

            $table->timestampTz('created_at');
            $table->timestampTz('updated_at');
        });

        DB::statement("
            ALTER TABLE users
            ADD CONSTRAINT users_role_check
            CHECK (role IN ('user', 'admin'))
        ");

        DB::statement("
            ALTER TABLE users
            ADD CONSTRAINT users_status_check
            CHECK (status IN ('active', 'locked'))
        ");

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email', 255)->primary();
            $table->string('token', 255);
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id', 255)->primary();

            $table->foreignId('user_id')
                ->nullable()
                ->index()
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('payload');

            $table->integer('last_activity')->index();
        });
    }

    /**
     * Xóa các bảng theo thứ tự phụ thuộc.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};