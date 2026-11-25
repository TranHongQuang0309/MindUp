<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PracticeContent\StorePersonalPracticeContentRequest;
use App\Http\Resources\PracticeContentResource;
use App\Models\PracticeContent;
use Illuminate\Http\JsonResponse;

use App\Http\Requests\PracticeContent\IndexPracticeContentRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

use App\Http\Requests\PracticeContent\UpdatePersonalPracticeContentRequest;
use Illuminate\Http\Request;

use App\Http\Requests\PracticeContent\UpdatePersonalPracticeContentStatusRequest;

class PersonalPracticeContentController extends Controller
{

    public function index(
        IndexPracticeContentRequest $request
        ): AnonymousResourceCollection {
            $data = $request->validated();
            $perPage = (int) ($data['per_page'] ?? 12);

            $contents = PracticeContent::query()
                ->where('created_by', $request->user()->id)
                ->where('scope', 'private')
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->paginate($perPage)
                ->withQueryString();

            return PracticeContentResource::collection($contents);
        }
    public function store(
        StorePersonalPracticeContentRequest $request
    ): JsonResponse {
        $data = $request->validated();

        $content = new PracticeContent();
        $content->creator()->associate($request->user());
        $content->title = $data['title'];
        $content->description = $data['description'];
        $content->guidance = $data['guidance'] ?? null;
        $content->scope = 'private';
        $content->status = 'available';
        $content->save();

        return (new PracticeContentResource($content))
            ->additional([
                'message' => 'Tạo nội dung luyện tập cá nhân thành công.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(
        Request $request,
        int $id
    ): PracticeContentResource {
        $content = $this->findOwnedContent(
            $id,
            $request->user()->id
        );

        return new PracticeContentResource($content);
    }

    public function update(
        UpdatePersonalPracticeContentRequest $request,
        int $id
    ): JsonResponse {
        $content = $this->findOwnedContent(
            $id,
            $request->user()->id
        );

        $data = $request->safe()->only([
            'title',
            'description',
            'guidance',
        ]);

        $content->fill($data);
        $content->save();

        return (new PracticeContentResource($content))
            ->additional([
                'message' => 'Cập nhật nội dung luyện tập cá nhân thành công.',
            ])
            ->response();
    }

    public function updateStatus(
        UpdatePersonalPracticeContentStatusRequest $request,
        int $id
    ): JsonResponse {
        $content = $this->findOwnedContent(
            $id,
            $request->user()->id
        );

        $data = $request->validated();

        $content->status = $data['status'];

        if ($content->isDirty('status')) {
            $content->save();
        }

        $message = $content->status === 'available'
            ? 'Nội dung luyện tập cá nhân đang được sử dụng.'
            : 'Nội dung luyện tập cá nhân đã ngừng sử dụng.';

        return (new PracticeContentResource($content))
            ->additional([
                'message' => $message,
            ])
            ->response();
    }


    private function findOwnedContent(
        int $id,
        int $userId
    ): PracticeContent {
        return PracticeContent::query()
            ->where('created_by', $userId)
            ->where('scope', 'private')
            ->findOrFail($id);
    }
    
}