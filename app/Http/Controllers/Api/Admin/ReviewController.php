<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProvider;
use App\Http\Requests\StatusRequest;
use App\Http\Resources\Admin\ReviewCollection;
use App\Http\Resources\Admin\ReviewOneResource;
use App\Jobs\ReviewPublishedJob;
use App\Jobs\UpdateSkuRatingJob;
use App\Models\Review;
use App\Models\Sku;
use App\Repositories\ReviewRepository\IReviewRepository;
use App\Services\EntityStatus;
use App\Services\ImageSavingService\ImageSavingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;


class ReviewController extends Controller
{
    use DataProvider;

    const IMAGES_FOLDER = 'public/image/premoderatedReviews/';

    /**
     * @param  IReviewRepository $reviewRepository
     * @param  Request $request
     * @return ReviewCollection
     */
    public function index(IReviewRepository $reviewRepository, Request $request): ReviewCollection
    {
        $perPage = (int)($request->per_page ?? 10);

        $query = $reviewRepository->getAdminReviewListQuery();

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
            $params['images'] = $imageSavingService->saveImages($request->images, self::IMAGES_FOLDER, $fileName);
        }

        $createdReview = Review::create($params);

        return response()->json([
            'data' => [
                'status' => 'success',
                'message' => 'Отзыв успешно создан',
                'data' => $createdReview
            ]
        ], Response::HTTP_CREATED);
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

            $params['images'] = $imageSavingService->saveImages($request->images, self::IMAGES_FOLDER, $fileName);
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
     * @return JsonResponse
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
     * @param  StatusRequest $request
     * @return JsonResponse
     */
    public function setStatus(int $id, StatusRequest $request): JsonResponse
    {
        $status = $request->input('status');
        $review = Review::query()
            ->with('sku')
            ->select('id', 'status', 'sku_id', 'user_id')
            ->find($id);


        if ($review) {
            Review::query()->where('id', $review->id)->update(['status' => $status]);

            if ($status === EntityStatus::PUBLISHED) {
                if ($review->sku->user_id === $review->user_id && $review->sku->status !== EntityStatus::PUBLISHED) {
                    $review->sku->update(['status' => EntityStatus::PUBLISHED]);
                }
                ReviewPublishedJob::dispatch($review->user_id, $review->id);
                UpdateSkuRatingJob::dispatch($review->sku, 'plus');
            } else if ($review->status === EntityStatus::PUBLISHED) {
                UpdateSkuRatingJob::dispatch($review->sku, 'minus');
            }
        }

        return response()->json(['data' => ['status' => 'success']]);
    }

    /**
     * @return JsonResponse
     */
    public function dynamic(): JsonResponse
    {
        $result = DB::table('reviews')
            ->select(DB::raw("count(id) AS count, DATE(created_at) AS date"))
            ->where('user_id', '<>', 1)
            ->groupBy(DB::raw('WEEK(created_at)'))
            ->orderBy('date')
            ->get();

        return response()->json(['data'=> $result]);
    }
}
