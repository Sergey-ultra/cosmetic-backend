<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProvider;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleSingleResource;
use App\Http\Resources\ArticleWithTagsCollection;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleComment;
use App\Models\ArticleView;
use App\Models\Tag;
use App\Models\User;
use App\Models\UserInfo;
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


    public function byTag(string $tag): ArticleWithTagsCollection
    {
        $perPage = (int)($request->per_page ?? 10);

        $result = Article::additionalInfoWithTagsWithJoinToTags()
            ->where(['tags.tag' => $tag, 'articles.status' => 'published'])
            ->orderBy('articles.created_at', 'DESC')
            ->paginate($perPage);


        return new ArticleWithTagsCollection($result, ['tag' => Tag::where('tag', $tag)->first()]);
    }

    public function byCategoryId(int $categoryId): ArticleWithTagsCollection
    {
        $perPage = (int)($request->per_page ?? 10);

        $result = Article::withTags()
            ->where(['article_categories.id' => $categoryId, 'articles.status' => 'published'])
            ->orderBy('articles.created_at', 'DESC')
            ->paginate($perPage);


        return new ArticleWithTagsCollection($result, ['category' => ArticleCategory::query()->find($categoryId)]);
    }

    public function show(Request $request, string $slug): ArticleSingleResource
    {
        $viewsCountSubQuery = DB::table('article_views')
            ->select([DB::raw('count(ip_address) as count'), 'article_id'])
            ->groupBy('article_id');

        $article = Article::query()->select(
            'articles.id',
            'articles.title',
            'articles.slug',
            'articles.preview',
            'articles.body',
            'articles.image',
            'articles.created_at',
            'users.name AS user_name',
            'article_categories.id AS category_id',
            'article_categories.name AS category_name',
            'article_categories.color AS category_color',
            'user_infos.avatar AS user_avatar',
            DB::raw('IF(article_views.count IS NOT NULL, article_views.count, 0) AS views_count')
        )
            ->with([
                'tags',
                'comments' => function ($query) {
                    $query
                        ->select([
                            sprintf('%s.id', ArticleComment::TABLE),
                            sprintf('%s.name AS user_name', User::TABLE),
                            sprintf('%s.avatar as user_avatar', UserInfo::TABLE),
                            'reply_id',
                            'article_id',
                            'comment',
                            DB::raw(sprintf('DATE(%s.created_at) AS created_at', ArticleComment::TABLE))
                        ])
                        ->with('likes')
                        ->leftJoin(
                            User::TABLE,
                            sprintf('%s.user_id', ArticleComment::TABLE),
                            '=',
                            sprintf('%s.id', User::TABLE)
                        )
                        ->leftjoin(
                            UserInfo::TABLE,
                            sprintf('%s.user_id', ArticleComment::TABLE),
                            '=',
                            sprintf('%s.user_id', UserInfo::TABLE)
                        )
                        ->where(sprintf('%s.status', ArticleComment::TABLE), ArticleComment::STATUS_PUBLISHED)
                        ->orderBy(sprintf('%s.created_at', ArticleComment::TABLE), 'DESC');
                }])
            ->leftJoinSub($viewsCountSubQuery, 'article_views', function ($join) {
                $join->on('articles.id', '=', 'article_views.article_id');
            })
            ->join('users', 'articles.user_id', '=', 'users.id')
            ->leftJoin('user_infos', 'users.id', '=', 'user_infos.user_id')
            ->leftJoin('article_categories', 'article_categories.id', '=', 'articles.article_category_id')
            ->where(['articles.slug' => $slug, 'articles.status' => Article::STATUS_PUBLISHED])
            ->first();




        ArticleView::query()->updateOrCreate([
            'article_id' => $article->id,
            'ip_address' => $request->ip()
        ], []);

        return new ArticleSingleResource($article);
    }

    /**
     * @return JsonResponse
     */
    public function articleCategories(): JsonResponse
    {
        $result = ArticleCategory::query()->select('id', 'name', 'color')->get();
        return response()->json(['data' => $result]);
    }

    /**
     * @return JsonResponse
     */
    public function tags(): JsonResponse
    {
        $result = Tag::query()->select('id', 'tag', 'parent_id')->get();
        return response()->json(['data' => $result]);
    }
}
