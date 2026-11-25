<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PracticeSession\StoreAudioRecordingRequest;
use App\Http\Resources\AudioRecordingResource;
use App\Services\Audio\StoreAudioRecordingService;
use Illuminate\Http\JsonResponse;

use App\Models\PracticeSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AudioRecordingController extends Controller
{
    public function store(
        StoreAudioRecordingRequest $request,
        int $sessionId,
        StoreAudioRecordingService $service
    ): JsonResponse {
        /** @var \Illuminate\Http\UploadedFile $file */
        $file = $request->file('audio');

        $recording = $service->store(
            $request->user(),
            $sessionId,
            $file
        );

        return (new AudioRecordingResource($recording))
            ->additional([
                'message' => 'Tải bản ghi âm thành công.',
            ])
            ->response()
            ->setStatusCode(201);
    }
        public function file(
    Request $request,
    int $sessionId,
    int $recordingId
): BinaryFileResponse {
    $session = PracticeSession::query()
        ->where('user_id', $request->user()->id)
        ->findOrFail($sessionId);

    $argument = $session->argument()->firstOrFail();

    $recording = $argument->audioRecordings()
        ->findOrFail($recordingId);

    $disk = Storage::disk(
        config('media.audio_disk', 'local')
    );

    if (! $disk->exists($recording->storage_path)) {
        abort(404, 'Tệp âm thanh không còn tồn tại.');
    }

    $extension = pathinfo(
        $recording->storage_path,
        PATHINFO_EXTENSION
    );

    $response = response()->file(
        $disk->path($recording->storage_path),
        [
            'Content-Type' => $recording->mime_type,
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]
    );

    $response->setContentDisposition(
        'inline',
        'recording-'.$recording->id.'.'.$extension
    );

    return $response;
}
}