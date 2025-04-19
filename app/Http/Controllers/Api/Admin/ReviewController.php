<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProviderWithDTO;
use App\Http\Controllers\Traits\ParamsDTO;
use App\Http\Requests\RejectReviewRequest;
use App\Http\Requests\ReviewRequest;
use App\Http\Requests\StatusRequest;
use App\Http\Resources\Admin\ReviewCollection;
use App\Http\Resources\Admin\ReviewOneResource;
use App\Jobs\ReviewPublishedJob;
use App\Jobs\UpdateSkuRatingJob;
use App\Models\Review;
use App\Models\User;
use App\Repositories\ReviewRepository\IReviewRepository;
use App\Services\ImageSavingService\ImageSavingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;


class ReviewController extends Controller
{
    use DataProviderWithDTO;

    const IMAGES_FOLDER = 'public/image/premoderatedReviews/';

    /**
     * @param  IReviewRepository $reviewRepository
     * @param  Request $request
     * @return ReviewCollection
     */
    public function index(IReviewRepository $reviewRepository, Request $request): ReviewCollection
    {
        $perPage = $request->integer('per_page', 10);

        $query = $reviewRepository->getAdminReviewListQuery();

        $paramsDto = new ParamsDTO(
            $request->input('filter', []),
            $request->input('sort', ''),
        );

        $result = $this->prepareModel($paramsDto, $query)->paginate($perPage);

        return new ReviewCollection($result);
    }

    public function store(ReviewRequest $request, ImageSavingService $imageSavingService): JsonResponse
    {
        $params = $request->all();

//        $params['images'] = null;
//        if ($request->has('images')) {
//            $fileName = 'review_' . $params['id'];
//            $params['images'] = $imageSavingService->saveImages($request->images, self::IMAGES_FOLDER, $fileName);
//        }

        $botUserIds = User::query()
            ->select('id')
            ->where('role_id', User::ROLE_BOT)
            ->get()
            ->pluck('id')
            ->all();

        $params['user_id'] = $botUserIds[rand(0, count($botUserIds) - 1)];
        $params['status'] = 'published';

        $createdReview = Review::query()->create($params);
        UpdateSkuRatingJob::dispatch($createdReview, 'plus');

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
            ->select(DB::raw("count(sku_id) AS count, DATE(created_at) AS date "))
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
        $review = Review::query()
            ->with('sku')
            ->select('id', 'status', 'sku_id', 'user_id')
            ->find($id);


        if ($review) {
            $status = $request->input('status');
            $review->update(['status' => $status]);

            if ($status === 'published') {
                if ($review->sku->user_id === $review->user_id && $review->sku->status !== 'published') {

                    $review->sku->update(['status' => 'published']);
                }

                ReviewPublishedJob::dispatch($review->user_id, $review->id);
                UpdateSkuRatingJob::dispatch($review, 'plus');
            } else if ($review->status === 'published') {
                UpdateSkuRatingJob::dispatch($review, 'minus');
            }
        }

        return response()->json(['data' => ['status' => 'success']]);
    }

    public function reject(int $id, RejectReviewRequest $request): JsonResponse
    {
        $review = Review::query()
            ->select('id', 'status', 'sku_id', 'user_id')
            ->find($id);

        if (!$review) {
            return response()->json(['message' => 'NotFound'], Response::HTTP_NOT_FOUND);
        }


        $reasonIds = $request->input('reason_ids');
        $review->update(['status' => 'rejected']);
        $review->rejectedReasons()->sync($reasonIds);

        if ($review->status === 'published') {
            UpdateSkuRatingJob::dispatch($review, 'minus');
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
