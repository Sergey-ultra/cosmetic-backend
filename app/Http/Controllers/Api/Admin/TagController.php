<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;


use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Services\TreeService\TreeInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TagController extends Controller
{
    public function index(): JsonResponse
    {
        $result = Tag::select('id', 'tag', 'parent_id')->get();
        return response()->json(['data' => $result]);
    }


    public function tree(TreeInterface $treeService): JsonResponse
    {
        $tags = Tag::select('id', 'tag', 'parent_id')->get()->toArray();

        $result = $treeService->buildTree($tags, 'parent_id');

        return response()->json(['data' => $result]);
    }


    public function show($id): JsonResponse
    {
        $result = Tag::select('id', 'tag', 'description', 'parent_id')->where('id', $id)->first();
        return response()->json(['data' => $result ]);
    }

    public function store(Request $request): JsonResponse
    {
        $new = Tag::create($request->all());

        return response()->json([
            'data' => [
                'status' => 'success',
                'data' => $new
            ]
        ], Response::HTTP_CREATED);
    }

    public function update(Request $request, Tag $tag): JsonResponse
    {
        if ($tag) {
            $tag->update($request->all());

            return response()->json([
                'status'=> true,
                'message' =>'Категория успешно обновлена'
            ]);
        }
        return response()->json([
            'status'=> true,
            'message' =>'Not found'
        ], 404);
    }

    public function destroy(int $id): void
    {
        Tag::destroy($id);
    }
}
