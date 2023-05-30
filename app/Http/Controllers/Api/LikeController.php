<?php

namespace App\Http\Controllers\Api;

use App\Data\EljurPyramid\Models\GIA\GIAFiles;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Review;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function createOrUpdate(Request $request, int $id): JsonResponse
    {
        $status = 'false';

        $conditions = [
            'likeable_id' => $id,
            'plus_ip_address' => $request->ip()
        ];


        $entity = match ($request->input('entity')) {
            'review' => Review::class,
            'article' => Article::class,
            'review_comment' => Comment::class,
            'article_comment' => ArticleComment::class,
        };


        $conditions = array_merge($conditions, ['likeable_type' => $entity]);

        $existingLike = Like::query()->where($conditions)->first();

        if (! $existingLike) {
            Like::query()->create($conditions);
            $status = 'success';
        }

        return response()->json(['data' => ['status' => $status]]);
    }
}
