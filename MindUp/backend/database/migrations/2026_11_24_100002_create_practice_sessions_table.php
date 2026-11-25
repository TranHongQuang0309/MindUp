<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('practice_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->constrained('users')->restrictOnDelete();
            $table->foreignId('practice_content_id')->index()->constrained('practice_contents')->restrictOnDelete();
            $table->string('content_title_snapshot', 255);
            $table->text('content_description_snapshot');
            $table->string('status', 20)->default('in_progress');
            $table->bigInteger('revision')->default(0);
            $table->timestampTz('started_at');
            $table->timestampTz('last_saved_at')->nullable();
            $table->timestampTz('completed_at')->nullable();
            $table->timestampTz('created_at');
            $table->timestampTz('updated_at');
        });

        DB::statement("ALTER TABLE practice_sessions ADD CONSTRAINT practice_sessions_status_check CHECK (status IN ('in_progress', 'completed'))");

        DB::statement("ALTER TABLE practice_sessions ADD CONSTRAINT practice_sessions_revision_check CHECK (revision >= 0)");

        DB::statement("ALTER TABLE practice_sessions ADD CONSTRAINT practice_sessions_completion_check CHECK ((status = 'completed') = (completed_at IS NOT NULL))");

        DB::statement("ALTER TABLE practice_sessions ADD CONSTRAINT practice_sessions_last_saved_check CHECK (last_saved_at IS NULL OR last_saved_at >= started_at)");

        DB::statement("ALTER TABLE practice_sessions ADD CONSTRAINT practice_sessions_completed_time_check CHECK (completed_at IS NULL OR completed_at >= started_at)");

        DB::statement("ALTER TABLE practice_sessions ADD CONSTRAINT practice_sessions_title_check CHECK (btrim(content_title_snapshot) <> '')");

        DB::statement("ALTER TABLE practice_sessions ADD CONSTRAINT practice_sessions_description_check CHECK (btrim(content_description_snapshot) <> '')");
    }

    public function down(): void
    {
        Schema::dropIfExists('practice_sessions');
    }
};
