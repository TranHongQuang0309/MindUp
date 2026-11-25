<?php

namespace App\Services\Audio;

use Illuminate\Validation\ValidationException;
use JsonException;
use RuntimeException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

class InspectAudioFileService
{
    /**
     * @return array{
     *     extension: string,
     *     mime_type: string,
     *     duration_seconds: float|null
     * }
     */
    public function inspect(string $path): array
    {
        $binary = config('media.ffprobe_binary');

        if (! is_string($binary) || ! is_file($binary)) {
            throw new RuntimeException(
                'Đường dẫn ffprobe chưa được cấu hình hợp lệ.'
            );
        }

        $process = new Process([
            $binary,
            '-v',
            'error',
            '-protocol_whitelist',
            'file,pipe',
            '-format_whitelist',
            'matroska,webm,ogg,mp3,wav,mov,mp4,m4a,3gp,3g2,mj2',
            '-show_entries',
            'format=format_name,duration:stream=codec_type,duration',
            '-of',
            'json',
            $path,
        ]);

        $process->setTimeout(15);

        try {
            $process->run();
        } catch (ProcessTimedOutException $exception) {
            throw ValidationException::withMessages([
                'audio' => 'Không thể kiểm tra tệp trong thời gian cho phép.',
            ]);
        }

        if (! $process->isSuccessful()) {
            throw ValidationException::withMessages([
                'audio' => 'Không đọc được tệp âm thanh hoặc định dạng không được hỗ trợ.',
            ]);
        }

        try {
            $metadata = json_decode(
                $process->getOutput(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException $exception) {
            throw ValidationException::withMessages([
                'audio' => 'Thông tin tệp âm thanh không hợp lệ.',
            ]);
        }

        if (! is_array($metadata)) {
            throw ValidationException::withMessages([
                'audio' => 'Không nhận được thông tin tệp âm thanh.',
            ]);
        }

        $streams = $metadata['streams'] ?? [];
        $hasAudio = false;

        foreach ($streams as $stream) {
            $type = $stream['codec_type'] ?? null;

            if ($type === 'video') {
                throw ValidationException::withMessages([
                    'audio' => 'Chỉ chấp nhận tệp âm thanh không có luồng hình ảnh.',
                ]);
            }

            if ($type === 'audio') {
                $hasAudio = true;
            }
        }

        if (! $hasAudio) {
            throw ValidationException::withMessages([
                'audio' => 'Tệp không chứa luồng âm thanh.',
            ]);
        }

        $formats = explode(
            ',',
            (string) ($metadata['format']['format_name'] ?? '')
        );

        $type = $this->resolveFileType($formats);

        $duration = $this->parseDuration(
            $metadata['format']['duration'] ?? null
        );

        if ($duration === null) {
            foreach ($streams as $stream) {
                if (($stream['codec_type'] ?? null) !== 'audio') {
                    continue;
                }

                $streamDuration = $this->parseDuration(
                    $stream['duration'] ?? null
                );

                if ($streamDuration !== null) {
                    $duration = max($duration ?? 0, $streamDuration);
                }
            }
        }

        return [
            'extension' => $type['extension'],
            'mime_type' => $type['mime_type'],
            'duration_seconds' => $duration,
        ];
    }

    /**
     * @param array<int, string> $formats
     * @return array{extension: string, mime_type: string}
     */
    private function resolveFileType(array $formats): array
    {
        $supported = [
            'mp3' => ['extension' => 'mp3', 'mime_type' => 'audio/mpeg'],
            'wav' => ['extension' => 'wav', 'mime_type' => 'audio/wav'],
            'ogg' => ['extension' => 'ogg', 'mime_type' => 'audio/ogg'],
            'webm' => ['extension' => 'webm', 'mime_type' => 'audio/webm'],
            'matroska' => ['extension' => 'webm', 'mime_type' => 'audio/webm'],
            'mov' => ['extension' => 'm4a', 'mime_type' => 'audio/mp4'],
            'mp4' => ['extension' => 'm4a', 'mime_type' => 'audio/mp4'],
        ];

        foreach ($supported as $format => $type) {
            if (in_array($format, $formats, true)) {
                return $type;
            }
        }

        throw ValidationException::withMessages([
            'audio' => 'Định dạng âm thanh chưa được hỗ trợ.',
        ]);
    }

    private function parseDuration(mixed $value): ?float
    {
        if (! is_numeric($value)) {
            return null;
        }

        $duration = (float) $value;

        if (
            ! is_finite($duration)
            || $duration <= 0
            || $duration > 9999999.999
        ) {
            return null;
        }

        return round($duration, 3);
    }
}