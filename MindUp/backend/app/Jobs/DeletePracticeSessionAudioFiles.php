<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DeletePracticeSessionAudioFiles implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /**
     * @param array<int, string> $paths
     */
    public function __construct(
        public array $paths
    ) {
    }

    public function backoff(): array
    {
        return [10, 60];
    }

    public function handle(): void
    {
        $disk = Storage::disk('local');

        foreach ($this->paths as $path) {
            if (! $disk->exists($path)) {
                continue;
            }

            if (! $disk->delete($path)) {
                throw new RuntimeException(
                    'Không thể xóa tệp âm thanh của phiên đã xóa.'
                );
            }
        }
    }
}