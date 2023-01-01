<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProvider;
use App\Http\Resources\Admin\ReviewCollection;
use App\Http\Resources\Admin\ReviewOneResource;
use App\Models\Review;
use App\Models\Sku;
use App\Models\SkuRating;
use App\Services\ImageSavingService\ImageSavingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ReviewController extends Controller
{
    use DataProvider;

    const IMAGES_FOLDER = 'public/image/premoderatedReviews/';

    /**
     *
     * @param  \Illuminate\Http\Request $request
     * @return \App\Http\Resources\Admin\ReviewCollection
     */
    public function index(Request $request): ReviewCollection
    {
        $perPage = (int)  ($request->per_page ?? 10);

        $query = DB::table('reviews')
            ->select([
            'reviews.id AS review_id',
            'sku_ratings.rating',
            'sku_ratings.id AS sku_rating_id',
            'sku_ratings.user_name AS user',
            'sku_ratings.status AS rating_status',
            'skus.id AS sku_id',
            'products.name',
            'products.code',
            'reviews.comment',
            'reviews.minus',
            'reviews.plus',
            'reviews.anonymously',
            'reviews.images',
            'reviews.status AS review_status'
        ])
            ->rightJoin('sku_ratings', 'reviews.sku_rating_id', '=', 'sku_ratings.id')
            ->join('skus', 'sku_ratings.sku_id', '=', 'skus.id')
            ->join('products', 'skus.product_id', '=', 'products.id')
            ->whereNotNull('sku_ratings.user_id')
        ;


        $result = $this->prepareModel($request, $query, true)->paginate($perPage);

        return new ReviewCollection($result);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Services\ImageSavingService\ImageSavingService $imageSavingService
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, ImageSavingService $imageSavingService): JsonResponse
    {
        $params = $request->all();

        $params['images'] = null;
        if ($request->has('images')) {
            $fileName = 'review_' . $params['id'];
            $params['images'] = $imageSavingService->imageSave($request->images, self::IMAGES_FOLDER, $fileName);
        }

        $createdReview = Review::create($params);

        return response()->json([
            'data' => [
                'status' => 'success',
                'message' => 'Отзыв успешно создан',
                'data' => $createdReview
            ]
        ], 201);
    }

    /**
     *
     * @param  \App\Models\Review $review
     * @return \Illuminate\Http\JsonResponse | \App\Http\Resources\Admin\ReviewOneResource
     */
    public function show(Review $review): JsonResponse|ReviewOneResource
    {
        if (!$review) {
            return response()->json(['data' => 'Not found'], 404);
        }
        return new ReviewOneResource($review);
    }

    /**
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Review $review
     * @param  \App\Services\ImageSavingService\ImageSavingService $imageSavingService
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Review $review, ImageSavingService $imageSavingService): JsonResponse
    {
        $params = $request->all();

        $params['images'] = null;
        if ($request->has('images') && count($request->images)) {

            $fileName = 'review_' . $params['id'];

            $params['images'] = $imageSavingService->imageSave($request->images, self::IMAGES_FOLDER, $fileName);
        }


        $review->update($params);

        return response()->json([
            'data' => [
                'status' => true,
                'message' => 'Отзыв успешно обновлен'
            ]
        ]);
    }

    /**
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function dynamics(): JsonResponse
    {
        $result = DB::table('reviews')
            ->select(DB::raw("count(sku_rating_id) AS count, DATE(created_at) AS date "))
            ->groupBy(DB::raw('WEEK(created_at)'))
            ->orderBy(DB::raw('WEEK(created_at)'))
            ->get();

        return response()->json(['data'=> $result]);
    }

    /**
     * @param int $id
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setStatus(int $id, Request $request): JsonResponse
    {
        $reviewInfo = SkuRating::select(
            'reviews.id AS review_id',
            'reviews.status AS review_status',
            'skus.id AS sku_id'
        )
            ->join('skus', 'skus.id', '=', 'sku_ratings.sku_id')
            ->leftJoin('reviews', 'sku_ratings.id', '=', 'reviews.sku_rating_id')
            ->where('sku_ratings.id', $id)
            ->first();


        if ($request->status === 'deleted') {
            SkuRating::where('id', $id)->update(['status' => 'deleted']);
        } else {
            SkuRating::where('id', $id)->update(['status' => 'published']);
        }

        if ($reviewInfo->review_id) {
            Review::where('id', $reviewInfo->review_id)->update(['status' => $request->status]);

            if ($request->status === 'published') {
                Sku::where('id', $reviewInfo->sku_id)->update(['reviews_count' => DB::raw('reviews_count + 1')]);
            } else if ($reviewInfo->review_status === 'published' && $request->status !== 'published') {
                Sku::where('id', $reviewInfo->sku_id)->update(['reviews_count' => DB::raw('reviews_count - 1')]);
            }
        }

        return response()->json(['data' => ['status' => 'success']]);
    }
}
