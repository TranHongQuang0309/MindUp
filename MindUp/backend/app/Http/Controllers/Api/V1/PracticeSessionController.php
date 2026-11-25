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


use App\Http\Requests\PracticeSession\IndexPracticeSessionRequest;
use App\Http\Resources\PracticeSessionSummaryResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

use App\Http\Requests\PracticeSession\DeletePracticeSessionRequest;
use App\Services\Practice\DeletePracticeSessionService;
use Illuminate\Http\Response;

class PracticeSessionController extends Controller
{


    public function index(
        IndexPracticeSessionRequest $request
    ): AnonymousResourceCollection {
        $data = $request->validated();
        $perPage = (int) ($data['per_page'] ?? 12);

        $query = PracticeSession::query()
            ->where('user_id', $request->user()->id);

        if (isset($data['status'])) {
            $query->where('status', $data['status']);
        }

        $sessions = $query
            ->select([
                'id',
                'practice_content_id',
                'content_title_snapshot',
                'status',
                'revision',
                'target_duration_seconds',
                'started_at',
                'last_saved_at',
                'completed_at',
                'created_at',
                'updated_at',
            ])
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return PracticeSessionSummaryResource::collection($sessions);
    }

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
            $data
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
        public function destroy(
            DeletePracticeSessionRequest $request,
            int $id,
            DeletePracticeSessionService $service
        ): Response {
            $data = $request->validated();

            $service->delete(
                $request->user(),
                $id,
                (int) $data['revision']
            );

            return response()->noContent();
        }

}