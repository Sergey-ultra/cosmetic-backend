<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;


use App\Http\Controllers\Controller;

use App\Http\Controllers\Traits\DataProviderWithDTO;
use App\Http\Controllers\Traits\ParamsDTO;
use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleCommentController extends Controller
{
    use DataProviderWithDTO;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int)($request->per_page ?? 10);

        $query = ArticleComment::query()
            ->select([
                sprintf('%s.id', ArticleComment::TABLE),
                sprintf('%s.comment', ArticleComment::TABLE),
                sprintf('%s.reply_id', ArticleComment::TABLE),
                sprintf('%s.status', ArticleComment::TABLE),
                sprintf('%s.created_at', ArticleComment::TABLE),
                sprintf('%s.title', Article::TABLE),
                sprintf('%s.name AS user_name', User::TABLE),
            ])
            ->join(
                Article::TABLE,
                sprintf('%s.article_id', ArticleComment::TABLE),
                '=',
                sprintf('%s.id', Article::TABLE)
            )
            ->leftJoin(
                User::TABLE,
                sprintf('%s.user_id', ArticleComment::TABLE),
                '=',
                sprintf('%s.id', User::TABLE)
            );

        $paramsDto = new ParamsDTO(
            $request->input('filter', []),
            $request->input('sort', ''),
        );

        $result = $this->prepareModel($paramsDto, $query)->paginate($perPage);

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
