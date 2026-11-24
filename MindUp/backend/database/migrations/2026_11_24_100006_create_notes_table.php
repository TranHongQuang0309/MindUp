<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_session_id')->index()->constrained('practice_sessions')->cascadeOnDelete();
            $table->text('body');
            $table->timestampTz('created_at');
            $table->timestampTz('updated_at');
        });

        DB::statement("ALTER TABLE notes ADD CONSTRAINT notes_body_check CHECK (btrim(body) <> '')");
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
