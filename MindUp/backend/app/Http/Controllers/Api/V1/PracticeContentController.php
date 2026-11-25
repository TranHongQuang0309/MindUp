<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PracticeContent\IndexPracticeContentRequest;
use App\Http\Resources\PracticeContentResource;
use App\Models\PracticeContent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PracticeContentController extends Controller
{
    public function index(
        IndexPracticeContentRequest $request
    ): AnonymousResourceCollection {
        $data = $request->validated();
        $perPage = (int) ($data['per_page'] ?? 12);

        $contents = PracticeContent::query()
            ->availableTo($request->user()->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return PracticeContentResource::collection($contents);
    }

    public function show(
        Request $request,
        int $id
    ): PracticeContentResource {
        $content = PracticeContent::query()
            ->availableTo($request->user()->id)
            ->findOrFail($id);

        return new PracticeContentResource($content);
    }

    public function random(Request $request): PracticeContentResource
    {
        $content = PracticeContent::query()
            ->availableTo($request->user()->id)
            ->inRandomOrder()
            ->first();

        if (! $content) {
            abort(404, 'Chưa có nội dung luyện tập phù hợp.');
        }

        return new PracticeContentResource($content);
    }
    
}