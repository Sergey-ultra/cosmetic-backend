<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProvider;
use App\Http\Requests\ArticleRequest;
use App\Http\Resources\Admin\ArticleCollection;
use App\Http\Resources\Admin\ArticleSingleResource;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Services\CodeService;
use App\Services\ImageSavingService\ImageSavingService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;


class ArticleController extends Controller
{
    use DataProvider;

    const IMAGES_FOLDER = 'public/image/articles/';

    /**
     *
     * @param  \Illuminate\Http\Request $request
     * @return \App\Http\Resources\Admin\ArticleCollection
     */
    public function index(Request $request): ArticleCollection
    {
        $perPage = (int) ($request->per_page ?? 10);

        $query = DB::table('articles')
            ->select(
            'articles.id as id',
            'articles.title as title',
            'articles.preview as preview',
            'article_categories.name AS category_name',
            'articles.status as status',
            'articles.created_at as created_at',
            'users.name as user'
            )
            ->leftJoin('article_categories', 'article_categories.id', '=', 'articles.article_category_id')
            ->join('users', 'articles.user_id', '=', 'users.id');

        $result = $this->prepareModel($request, $query, true)->paginate($perPage);

        return new ArticleCollection($result);
    }


    public function articleCategories(): JsonResponse
    {
        $result = ArticleCategory::select('id', 'name', 'color')->get();
        return response()->json(['data' => $result]);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): ArticleSingleResource
    {
        $article = Article::select('id', 'title', 'slug', 'preview', 'body', 'article_category_id', 'status', 'image')
            ->where('id', $id)
            ->first();


        return new ArticleSingleResource($article);
    }


    /**
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Services\ImageSavingService\ImageSavingService $imageSavingService
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ArticleRequest $request, ImageSavingService $imageSavingService): JsonResponse
    {
        $params = $request->all();
        $params['user_id'] = Auth::id();
        $slugName = CodeService::makeCode($params['title']);
        $params['slug'] = $slugName;

        $params['image'] = [];
        if ($request->has('image')) {
            $params['image'] = $imageSavingService->saveOneImage($request->image, self::IMAGES_FOLDER, $slugName);
        }


        $article = Article::create($params);

        if ($request->tags_ids) {
            foreach($request->tags_ids as $tagId) {
                $article->tags->create(['tag_id' => $tagId]);
            }
        }

        return response()->json([
            'data' => [
                'status' => true,
                'data' => $article
            ]
        ], 201);
    }


    /**
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Article $article
     * @param  \App\Services\ImageSavingService\ImageSavingService $imageSavingService
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ArticleRequest $request, Article $article, ImageSavingService $imageSavingService): JsonResponse
    {
        $params = $request->all();
        $slugName = CodeService::makeCode($params['title']);
        $params['slug'] = $slugName;

        $params['image'] = [];
        if ($request->has('image')) {
            $params['image'] = $imageSavingService->saveOneImage($request->image, self::IMAGES_FOLDER, $slugName);
        }

        $article->update($params);

        if ($request->has('tag_ids')) {

            $article->tags()->sync($request->tag_ids);

//            foreach($request->tags_ids as $tagId) {
//                $article->tags->create(['tag_id' => $tagId]);
//            }
        }


        return response()->json([
            'data' => [
                'status' => true,
                'message' => 'Статья успешно обновлена'
            ]
        ]);
    }

    /**
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function publish(int $id): JsonResponse
    {
        $article = Article::find($id);
        if (!$article) {
            return response()->json(['data' => ['status' => false]], 404);
        }
        $article->update(['status' => 'published']);

        return response()->json(['data' => ['status' => true]]);
    }

    /**
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function withdrawFromPublication(int $id): JsonResponse
    {
        $article = Article::find($id);
        if (!$article) {
            return response()->json(['data' => ['status' => false]], 404);
        }
        $article->update(['status' => 'moderated']);

        return response()->json(['data' => ['status' => true]]);
    }

    /**
     *
     * @param  int $id
     */
    public function destroy(int $id): void
    {
        Article::destroy($id);
    }
}
