<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('argument_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('argument_id')->index()->constrained('arguments')->cascadeOnDelete();
            $table->integer('version_number');
            $table->text('body_snapshot')->nullable();
            $table->bigInteger('audio_recording_id')->nullable();
            $table->timestampTz('created_at');
            $table->unique(['argument_id', 'version_number']);
        });

        DB::statement("ALTER TABLE argument_versions ADD CONSTRAINT argument_versions_number_check CHECK (version_number > 0)");

        DB::statement("ALTER TABLE argument_versions ADD CONSTRAINT argument_versions_content_check CHECK (COALESCE(btrim(body_snapshot), '') <> '' OR audio_recording_id IS NOT NULL)");
    }

    public function down(): void
    {
        Schema::dropIfExists('argument_versions');
    }
};
