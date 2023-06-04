<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProvider;
use App\Http\Requests\ArticleRequest;
use App\Http\Resources\Admin\ArticleCollection;
use App\Http\Resources\Admin\ArticleSingleResource;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Services\ArticleService\IArticle;
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
     * @param  IArticle $articleService
     * @param  Request $request
     * @return ArticleCollection
     */
    public function index(IArticle $articleService, Request $request): ArticleCollection
    {
        $perPage = (int)($request->per_page ?? 10);

        $query = $articleService->getAdminArticleList();

        $result = $this->prepareModel($request, $query, true)->paginate($perPage);

        return new ArticleCollection($result);
    }


    public function articleCategories(): JsonResponse
    {
        $result = ArticleCategory::query()->select('id', 'name', 'color')->get();
        return response()->json(['data' => $result]);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return ArticleSingleResource
     */
    public function show(int $id): ArticleSingleResource
    {
        $article = Article::query()
            ->select('id', 'title', 'slug', 'preview', 'body', 'article_category_id', 'status', 'image', 'user_id')
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
            $params['image'] = $imageSavingService->saveOneImage(
                $request->input('image'),
                self::IMAGES_FOLDER,
                $slugName
            );
        }


        $article = Article::create($params);

        if ($request->input('tags_ids')) {
            foreach($request->input('tags_ids') as $tagId) {
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
        $article->update(['status' => Article::STATUS_PUBLISHED]);

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
        $article->update(['status' => Article::STATUS_MODERATED]);

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
