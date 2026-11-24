<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('arguments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_session_id')->unique()->constrained('practice_sessions')->cascadeOnDelete();
            $table->text('body')->nullable();
            $table->bigInteger('current_recording_id')->nullable();
            $table->timestampTz('created_at');
            $table->timestampTz('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arguments');
    }
};
