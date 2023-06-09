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
        $entity = match ($request->input('entity')) {
            'review' => Review::class,
            'article' => Article::class,
            'review_comment' => Comment::class,
            'article_comment' => ArticleComment::class,
        };

        $conditions = [
            'likeable_type' => $entity,
            'likeable_id' => $id,
        ];

        $existingLikesCollection = Like::query()->where($conditions)->get();
        $existingCount = $existingLikesCollection->count();

        $existingLikeByIp = $existingLikesCollection->where( 'ip_address', $request->ip())->first();

        if (! $existingLikeByIp) {
            $conditions['ip_address'] = $request->ip();
            Like::query()->create($conditions);
            ++$existingCount;
            $isVote = true;
        } else {
            $existingLikeByIp->delete();
            --$existingCount;
            $isVote = false;
        }

        return response()->json([
            'data' => [
                'is_vote' => $isVote,
                'count' => $existingCount,
            ]
        ]);
    }
}
