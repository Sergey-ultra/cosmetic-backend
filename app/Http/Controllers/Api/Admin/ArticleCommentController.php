<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProvider;
use App\Models\ArticleComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleCommentController extends Controller
{
    use DataProvider;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int)($request->per_page ?? 10);

        $query = ArticleComment::select(
            'article_comments.id',
            'articles.title',
            'article_comments.user_name',
            'article_comments.comment',
            'article_comments.reply_id',
            'article_comments.status',
            'article_comments.created_at'
        )
        ->join('articles', 'articles.id', '=', 'article_comments.article_id');

        $result = $this->prepareModel($request, $query, true)->paginate($perPage);

        return response()->json(['data' => $result]);
    }

    /**
     * @param int $id
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setStatus(int $id, Request $request): JsonResponse
    {
        $comment = ArticleComment::find($id);
        if (!$comment) {
            return response()->json(['data' => ['status' => false]], 404);
        }

        $comment->update(['status' => $request->status]);

        return response()->json(['data' => ['status' => true]]);
    }




    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy(int $id): void
    {
        ArticleComment::where('id', $id)->update(['status' => 'deleted']);
    }
}
