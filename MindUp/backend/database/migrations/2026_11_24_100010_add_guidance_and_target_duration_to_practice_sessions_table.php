<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('practice_sessions', function (Blueprint $table) {
            $table->text('content_guidance_snapshot')->nullable();
            $table->integer('target_duration_seconds')->nullable();
        });

        DB::statement("
            ALTER TABLE practice_sessions
            ADD CONSTRAINT practice_sessions_target_duration_check
            CHECK (
                target_duration_seconds IS NULL
                OR target_duration_seconds IN (300, 600, 900)
            )
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE practice_sessions
            DROP CONSTRAINT practice_sessions_target_duration_check
        ");

        Schema::table('practice_sessions', function (Blueprint $table) {
            $table->dropColumn([
                'content_guidance_snapshot',
                'target_duration_seconds',
            ]);
        });
    }
};