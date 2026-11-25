<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('practice_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->index()->constrained('users')->restrictOnDelete();
            $table->string('title', 255);
            $table->text('description');
            $table->text('guidance')->nullable();
            $table->string('scope', 20);
            $table->string('status', 20)->default('available');
            $table->timestampTz('created_at');
            $table->timestampTz('updated_at');
            $table->index(['created_by', 'scope', 'status']);
            $table->index(['scope', 'status']);
        });

        DB::statement("ALTER TABLE practice_contents ADD CONSTRAINT practice_contents_scope_check CHECK (scope IN ('private', 'shared'))");

        DB::statement("ALTER TABLE practice_contents ADD CONSTRAINT practice_contents_status_check CHECK (status IN ('available', 'unavailable'))");

        DB::statement("ALTER TABLE practice_contents ADD CONSTRAINT practice_contents_title_check CHECK (btrim(title) <> '')");

        DB::statement("ALTER TABLE practice_contents ADD CONSTRAINT practice_contents_description_check CHECK (btrim(description) <> '')");
    }

    public function down(): void
    {
        Schema::dropIfExists('practice_contents');
    }
};
