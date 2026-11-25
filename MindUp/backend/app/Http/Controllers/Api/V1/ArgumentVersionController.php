<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PracticeSession\IndexArgumentVersionRequest;
use App\Http\Resources\ArgumentVersionResource;
use App\Models\Argument;
use App\Models\PracticeSession;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ArgumentVersionController extends Controller
{
    public function index(
        IndexArgumentVersionRequest $request,
        int $sessionId
    ): AnonymousResourceCollection {
        $argument = $this->findOwnedArgument(
            $sessionId,
            $request->user()->id
        );

        $data = $request->validated();
        $perPage = (int) ($data['per_page'] ?? 12);

        $versions = $argument->versions()
            ->orderByDesc('version_number')
            ->paginate($perPage)
            ->withQueryString();

        return ArgumentVersionResource::collection($versions);
    }

    public function show(
        Request $request,
        int $sessionId,
        int $versionId
    ): ArgumentVersionResource {
        $argument = $this->findOwnedArgument(
            $sessionId,
            $request->user()->id
        );

        $version = $argument->versions()
            ->findOrFail($versionId);

        return new ArgumentVersionResource($version);
    }

    private function findOwnedArgument(
        int $sessionId,
        int $userId
    ): Argument {
        $session = PracticeSession::query()
            ->where('user_id', $userId)
            ->findOrFail($sessionId);

        return $session->argument()->firstOrFail();
    }
}