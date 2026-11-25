<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('reference_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_session_id')->index()->constrained('practice_sessions')->cascadeOnDelete();
            $table->string('source_name', 255);
            $table->text('url')->nullable();
            $table->text('citation')->nullable();
            $table->timestampTz('created_at');
            $table->timestampTz('updated_at');
        });

        DB::statement("ALTER TABLE reference_sources ADD CONSTRAINT reference_sources_name_check CHECK (btrim(source_name) <> '')");
    }

    public function down(): void
    {
        Schema::dropIfExists('reference_sources');
    }
};
