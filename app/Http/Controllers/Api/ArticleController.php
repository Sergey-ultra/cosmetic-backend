<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProviderWithDTO;
use App\Http\Controllers\Traits\ParamsDTO;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleSingleResource;
use App\Http\Resources\ArticleWithTagsCollection;
use App\Jobs\ArticleViewJob;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Tag;
use App\Repositories\ArticleRepository\IArticleRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ArticleController extends Controller
{
    use DataProviderWithDTO;

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
            ->orderBy('articles.created_at', 'DESC');

        $paramsDto = new ParamsDTO(
            $request->input('filter', []),
            $request->input('sort', ''),
        );

        $result = $this->prepareModel($paramsDto, $query)->paginate($perPage);

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

    public function byCategoryId(Request $request, int $categoryId): ArticleWithTagsCollection
    {
        $perPage = (int)($request->per_page ?? 10);

        $result = Article::withTags()
            ->where(['article_categories.id' => $categoryId, 'articles.status' => 'published'])
            ->orderBy('articles.created_at', 'DESC')
            ->paginate($perPage);


        return new ArticleWithTagsCollection($result, ['category' => ArticleCategory::query()->find($categoryId)]);
    }

    /**
     * @param Request $request
     * @param string $slug
     * @param IArticleRepository $articleRepository
     * @return ArticleSingleResource
     */
    public function show(Request $request, string $slug, IArticleRepository $articleRepository): ArticleSingleResource
    {
        $article = $articleRepository->getSingleArticleBySlug($slug);

        ArticleViewJob::dispatch($article->id, $request->ip());

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
