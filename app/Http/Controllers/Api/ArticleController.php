<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProvider;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleSingleResource;
use App\Http\Resources\ArticleWithTagsCollection;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleView;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ArticleController extends Controller
{
    use DataProvider;

    public function index(Request $request): ArticleWithTagsCollection
    {
        $perPage = (int)($request->per_page ?? 10);

        $result = Article::withTags()
            ->where('articles.status', 'published')
            ->orderBy('articles.created_at', 'DESC')
            ->paginate($perPage);

        return new ArticleWithTagsCollection($result);
    }

    public function last(): ArticleCollection
    {
        $result = Article::withFullInfo()
            ->where('articles.status', 'published')
            ->orderBy('articles.created_at', 'DESC')
            ->limit(10)
            ->get();

        return new ArticleCollection($result);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function my(Request $request): JsonResponse
    {
        $perPage = (int)($request->per_page ?? 10);

        $query = Article::withFullInfo()
            ->where([
                ['articles.status', '<>', 'deleted'],
                'articles.user_id' => Auth::id()
            ])
            ->orderBy('articles.created_at', 'DESC')
        ;

        $result = $this->prepareModel($request, $query, true)->paginate($perPage);

        return response()->json([ 'data'=> $result ]);
    }


    public function byTag(string $tag)
    {
        $perPage = (int)($request->per_page ?? 10);

        $result = Article::additionalInfoWithTagsWithJoinToTags()
            ->where(['tags.tag' => $tag, 'articles.status' => 'published'])
            ->orderBy('articles.created_at', 'DESC')
            ->paginate($perPage);


        return new ArticleWithTagsCollection($result, ['tag' => Tag::where('tag', $tag)->first()]);
    }

    public function show(Request $request, string $slug)
    {
        $viewsCountSubQuery = DB::table('article_views')
            ->select([DB::raw('count(ip_address) as count'), 'article_id'])
            ->groupBy('article_id');

        $article = Article::select(
            'articles.id',
            'articles.title',
            'articles.slug',
            'articles.preview',
            'articles.body',
            'articles.image',
            'articles.created_at',
            'users.name AS user_name',
            'user_infos.avatar AS user_avatar',
             DB::raw('IF(article_views.count IS NOT NULL, article_views.count, 0) AS views_count')
        )
            ->with([
                'tags',
                'comments' => function ($query) {
                    $query
                        ->select(
                            'id',
                            'user_name',
                            'user_avatar',
                            'reply_id',
                            'article_id',
                            'comment',
                            DB::raw('DATE(created_at) AS created_at')
                        )
                        ->where('status', 'published')
                        ->orderBy('created_at', 'DESC');
                }])
            ->leftJoinSub($viewsCountSubQuery, 'article_views', function ($join) {
                $join->on('articles.id', '=', 'article_views.article_id');
            })
            ->join('users', 'articles.user_id', '=', 'users.id')
            ->leftJoin('user_infos', 'users.id', '=', 'user_infos.user_id')
            ->where(['articles.slug' => $slug, 'articles.status' => 'published'])
            ->first();




        ArticleView::updateOrCreate([
            'article_id' => $article->id,
            'ip_address' => $request->ip()
        ],[]);

        return new ArticleSingleResource($article);
    }

    /**
     * @return JsonResponse
     */
    public function articleCategories(): JsonResponse
    {
        $result = ArticleCategory::select('id', 'name', 'color')->get();
        return response()->json(['data' => $result]);
    }

    /**
     * @return JsonResponse
     */
    public function tags(): JsonResponse
    {
        $result = Tag::select('id', 'tag', 'parent_id')->get();
        return response()->json(['data' => $result]);
    }
}
