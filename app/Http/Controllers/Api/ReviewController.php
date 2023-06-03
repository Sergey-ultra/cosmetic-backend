<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProvider;
use App\Http\Requests\ReviewRequest;
use App\Http\Resources\MyReviewsCollection;
use App\Http\Resources\ReviewCollection;
use App\Http\Resources\ReviewSingleResource;
use App\Jobs\AdminNotificationJob;
use App\Models\Review;
use App\Models\ReviewView;
use App\Models\Sku;
use App\Models\SkuRating;
use App\Models\SkuVideo;
use App\Services\ReviewService\IReview;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ReviewController extends Controller
{
    use DataProvider;

    public const LAST_LIMIT = 10;

    /**
     * @param IReview $reviewService
     * @param Request $request
     * @return ResourceCollection
     */
    public function my(IReview $reviewService, Request $request): ResourceCollection
    {
        $perPage = (int)($request->per_page ?? 10);
        $query = $reviewService
            ->getReviewWithProductInfoQuery()
            ->where([
            'sku_ratings.user_id' => Auth::guard('api')->id(),
            'sku_ratings.status' => 'published'
        ]);

        $result = $this->prepareModel($request, $query)->paginate($perPage);

        return new MyReviewsCollection($result);
    }

    /**
     * @param IReview $reviewService
     * @return MyReviewsCollection
     */
    public function last(IReview $reviewService): MyReviewsCollection
    {
        $result = $reviewService
            ->getReviewWithProductInfoQuery()
            ->orderBy('sku_ratings.created_at', 'DESC')
            ->limit(self::LAST_LIMIT)
            ->get();

        return new MyReviewsCollection($result);
    }

    /**
     * @param  IReview $reviewService
     * @param  int $id
     * @param  Request $request
     * @return ResourceCollection
     */
    public function bySkuId(IReview $reviewService, Request $request, int $id): ResourceCollection
    {
        $perPage = (int)($request->per_page ?? 10);

        $query = $reviewService
            ->getReviewWithCommentCountQuery()
            ->where([
                'sku_ratings.sku_id' => $id,
                'reviews.status' => 'published'
            ]);

        $result = $this->prepareModel($request, $query)->paginate($perPage);

        return new ReviewCollection($result);
    }

    /**
     * @param IReview $reviewService
     * @param Request $request
     * @param int $id
     * @return ReviewSingleResource|JsonResponse
     */
    public function show(IReview $reviewService, Request $request, int $id): ReviewSingleResource|JsonResponse
    {
        $result = $reviewService->getSingleReview($id);

        if (!$result) {
            return response()->json([], Response::HTTP_NOT_FOUND);
        }


        ReviewView::query()->updateOrCreate([
            'review_id' => $result->id,
            'ip_address' => $request->ip()
        ], []);

        return new ReviewSingleResource($result);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function additionalInfoBySkuId(int $id): JsonResponse
    {
        $videos = SkuVideo::query()
            ->where(['sku_id' => $id, 'status' => 'published'])
            ->get();


        $info = $videos->reduce(
            function(array $common, SkuVideo $skuVideo): array {
                $common[] = [
                    'type' => 'video',
                    'video' => $skuVideo->video,
                    'url' => $skuVideo->thumbnail
                ];

                return $common;
            },
            []
        );


        $reviews = Review::query()
            ->select(
                'reviews.images',
                'sku_ratings.rating'
            )
            ->join('sku_ratings', 'sku_ratings.id', '=', 'reviews.sku_rating_id')
            ->where([
                'sku_ratings.sku_id' => $id,
                'reviews.status' => 'published'
            ])
            ->get();



        $info = $reviews->reduce(
            function(array $common, Review $review): array {
                if ($review->images && count($review->images)) {
                    foreach ( $review->images as $image) {
                        $common[] = ['type' => 'image', 'url' => $image];
                    }
                }
                return $common;
            },
            $info
        );


        $ratingFilter = $reviews
            ->groupBy('rating')
            ->map(function ($group) {
                return count($group);
            })->all();

        return response()->json(['data'=>
            [
                'info' => $info,
                'rating_filter' => $ratingFilter
            ]
        ]);
    }

    /**
     * check Existing Review.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function checkExistingReview(Request $request): JsonResponse
    {
        $conditions[] = ['sku_ratings.sku_id', '=', $request->sku_id];

        $user = Auth::guard('api')->user();
        if (isset($user)) {
           $conditions[] = ['sku_ratings.user_id' , '=', $user->id];
        } else {
            $conditions[] = ['sku_ratings.ip_address', '=', $request->ip()];
        }
        //$conditions[] = ['reviews.status', '<>', 'deleted'];

        $existingReview = Review::query()
            ->select([
                'sku_ratings.rating',
                'reviews.id',
                'reviews.title',
                'reviews.body',
                'reviews.plus',
                'reviews.minus',
                'reviews.images',
                'reviews.status',
                'reviews.anonymously',
                'reviews.is_recommend',
            ])
            ->rightJoin('sku_ratings', function ($join) {
                $join->on('reviews.sku_rating_id', '=', 'sku_ratings.id')
                    ->where('reviews.status', '!=', 'deleted');
            })
            ->where($conditions)
            ->first();

//        if ($existingUserRating && $existingUserRating->availableReview) {
//            $review = $existingUserRating->availableReview;
//        } else {
//            $review = null;
//        }

        return response()->json(['data'=> $existingReview]);
    }





    public function updateOrCreate(ReviewRequest $request): JsonResponse
    {
        $skuId = $request->sku_id;

        $currentRating = SkuRating::query()->where([
            'sku_id' => $skuId,
            'user_id' => Auth::guard('api')->user()->id
        ])->first();


        $currentSku = Sku::find($skuId);

        if (!$currentRating || !$currentSku) {
            return response()->json([
                'data' => [
                    'status' => 'success',
                    'message' => 'Рейтинг не существует'
                ]
            ], 404);
        }

        $review = Review::updateOrCreate(
            [
                'sku_rating_id' => $currentRating->id
            ],
            [
                'status' => 'moderated',
                'title' => $request->input('title'),
                'body' => mb_convert_encoding($request->input('body'), "UTF-8", "auto"),
                'plus' => $request->input('plus'),
                'minus' => $request->input('minus'),
                'images' => $request->input('images'),
                'anonymously' => $request->input('anonymously') ?? 0,
                'is_recommend' => $request->input('is_recommend') ?? 0,
            ]
        );

        if (request()->ip() !== config('telegrambot.admin_ip')) {
            $message = sprintf("Добавлен/обновлен отзыв с id %d", $review->id);
            AdminNotificationJob::dispatch($message);
        }

        return response()->json([
            'data' => [
                'status' => 'success',
                'data' => $review,
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id)
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

        SkuRating::where('id', $id)->update(['status' => 'deleted']);
        if ($reviewInfo->review_id) {
            if ($reviewInfo->review_status === 'published') {
                Sku::where('id', $reviewInfo->sku_id)->update(['reviews_count' => DB::raw('reviews_count - 1')]);
            }

            Review::where('id', $reviewInfo->review_id)->update(['status' => 'deleted']);
        }
    }
}
