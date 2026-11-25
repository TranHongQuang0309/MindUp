<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PracticeSession\SavePracticeSessionRequest;
use App\Http\Requests\PracticeSession\StorePracticeSessionRequest;
use App\Http\Resources\PracticeSessionResource;
use App\Models\PracticeSession;
use App\Services\Practice\SavePracticeSessionService;
use App\Services\Practice\StartPracticeSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Requests\PracticeSession\CompletePracticeSessionRequest;
use App\Services\Practice\CompletePracticeSessionService;

class PracticeSessionController extends Controller
{
    public function store(
        StorePracticeSessionRequest $request,
        StartPracticeSessionService $service
    ): JsonResponse {
        $data = $request->validated();

        $targetDuration = isset($data['target_duration_seconds'])
            ? (int) $data['target_duration_seconds']
            : null;

        $session = $service->start(
            $request->user(),
            (int) $data['practice_content_id'],
            $targetDuration
        );

        return (new PracticeSessionResource($session))
            ->additional([
                'message' => 'Bắt đầu phiên luyện tập thành công.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(
        Request $request,
        int $id
    ): PracticeSessionResource {
        $session = PracticeSession::query()
            ->where('user_id', $request->user()->id)
            ->with([
                'argument',
                'notes',
                'referenceSources',
            ])
            ->findOrFail($id);

        return new PracticeSessionResource($session);
    }

    public function update(
        SavePracticeSessionRequest $request,
        int $id,
        SavePracticeSessionService $service
    ): JsonResponse {
        $data = $request->validated();

        $session = $service->save(
            $request->user(),
            $id,
            (int) $data['revision'],
            $data['body'] ?? null,
            $data['notes'] ?? null,
            $data['reference_sources'] ?? null
        );

        return (new PracticeSessionResource($session))
            ->additional([
                'message' => 'Lưu bản nháp luyện tập thành công.',
            ])
            ->response();
    }

        public function complete(
            CompletePracticeSessionRequest $request,
            int $id,
            CompletePracticeSessionService $service
        ): JsonResponse {
            $data = $request->validated();

            $session = $service->complete(
                $request->user(),
                $id,
                (int) $data['revision']
            );

            return (new PracticeSessionResource($session))
                ->additional([
                    'message' => 'Phiên luyện tập đã hoàn thành.',
                ])
                ->response();
        }
        
    
}