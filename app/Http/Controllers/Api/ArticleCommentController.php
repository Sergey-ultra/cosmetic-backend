<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProviderWithDTO;
use App\Http\Controllers\Traits\ParamsDTO;
use App\Http\Requests\ArticleCommentRequest;
use App\Models\ArticleComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ArticleCommentController extends Controller
{
    use DataProviderWithDTO;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function my(Request $request): JsonResponse
    {
        $perPage = (int)($request->per_page ?? 10);

        $query = ArticleComment::query()
            ->select([
                'articles.title as body',
                DB::raw("CONCAT('/article/',articles.slug) AS url"),
                'article_comments.comment',
                'article_comments.id',
                'article_comments.status',
            ])
            ->join('articles', 'article_comments.article_id', '=', 'articles.id')
            ->where('article_comments.user_id', Auth::id())
            ->where('article_comments.status', '<>', 'deleted');

        $paramsDto = new ParamsDTO(
            $request->input('filter', []),
            $request->input('sort', ''),
        );

        $result = $this->prepareModel($paramsDto, $query)->paginate($perPage);

        return response()->json([ 'data'=> $result ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ArticleCommentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ArticleCommentRequest $request): JsonResponse
    {
        $params = $request->all();
        $user = Auth::user();
        $params['user_id'] = $user->id;


        $newRecord = ArticleComment::query()->create($params);

        return response()->json([
            'data' => [
                'status' => true,
                'data' => $newRecord
            ]
        ], Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy(int $id)
    {
        ArticleComment::where('id', $id)->update(['status' => 'deleted']);
    }
}
