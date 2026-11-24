<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('ai_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('argument_id')->index()->constrained('arguments')->cascadeOnDelete();
            $table->text('input_text');
            $table->text('response_text');
            $table->timestampTz('requested_at');
            $table->timestampTz('responded_at');
            $table->index(['argument_id', 'responded_at']);
        });

        DB::statement("ALTER TABLE ai_feedbacks ADD CONSTRAINT ai_feedbacks_input_check CHECK (btrim(input_text) <> '')");

        DB::statement("ALTER TABLE ai_feedbacks ADD CONSTRAINT ai_feedbacks_response_check CHECK (btrim(response_text) <> '')");

        DB::statement("ALTER TABLE ai_feedbacks ADD CONSTRAINT ai_feedbacks_time_check CHECK (responded_at >= requested_at)");
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_feedbacks');
    }
};
