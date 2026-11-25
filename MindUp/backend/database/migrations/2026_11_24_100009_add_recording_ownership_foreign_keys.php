<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Check at transaction end so deleting the whole session can cascade safely.
        DB::statement(<<<'SQL'
            ALTER TABLE arguments
            ADD CONSTRAINT arguments_current_recording_owner_fk
            FOREIGN KEY (id, current_recording_id)
            REFERENCES audio_recordings (argument_id, id)
            MATCH SIMPLE
            ON DELETE NO ACTION
            DEFERRABLE INITIALLY DEFERRED
            SQL);

        DB::statement(<<<'SQL'
            ALTER TABLE argument_versions
            ADD CONSTRAINT argument_versions_recording_owner_fk
            FOREIGN KEY (argument_id, audio_recording_id)
            REFERENCES audio_recordings (argument_id, id)
            MATCH SIMPLE
            ON DELETE NO ACTION
            DEFERRABLE INITIALLY DEFERRED
            SQL);
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE argument_versions DROP CONSTRAINT argument_versions_recording_owner_fk');
        DB::statement('ALTER TABLE arguments DROP CONSTRAINT arguments_current_recording_owner_fk');
    }
};
