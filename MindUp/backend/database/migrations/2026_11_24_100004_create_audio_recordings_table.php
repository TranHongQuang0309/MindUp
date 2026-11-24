<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('audio_recordings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('argument_id')->index()->constrained('arguments')->cascadeOnDelete();
            $table->text('storage_path');
            $table->string('mime_type', 100);
            $table->bigInteger('size_bytes');
            $table->decimal('duration_seconds', 10, 3)->nullable();
            $table->string('transcription_status', 20)->default('pending');
            $table->text('transcript')->nullable();
            $table->text('transcription_error')->nullable();
            $table->timestampTz('created_at');
            $table->timestampTz('updated_at');
            $table->unique(['argument_id', 'id'], 'audio_recordings_argument_id_id_unique');
        });

        DB::statement("ALTER TABLE audio_recordings ADD CONSTRAINT audio_recordings_size_check CHECK (size_bytes > 0)");

        DB::statement("ALTER TABLE audio_recordings ADD CONSTRAINT audio_recordings_duration_check CHECK (duration_seconds IS NULL OR duration_seconds >= 0)");

        DB::statement("ALTER TABLE audio_recordings ADD CONSTRAINT audio_recordings_status_check CHECK (transcription_status IN ('pending', 'processing', 'completed', 'failed'))");

        DB::statement("ALTER TABLE audio_recordings ADD CONSTRAINT audio_recordings_path_check CHECK (btrim(storage_path) <> '')");

        DB::statement("ALTER TABLE audio_recordings ADD CONSTRAINT audio_recordings_mime_check CHECK (btrim(mime_type) <> '')");
    }

    public function down(): void
    {
        Schema::dropIfExists('audio_recordings');
    }
};
