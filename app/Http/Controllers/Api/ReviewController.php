<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProvider;
use App\Http\Requests\ReviewRequest;
use App\Http\Resources\LastReviewsCollection;
use App\Http\Resources\MyReviewsCollection;
use App\Http\Resources\ReviewCollection;
use App\Http\Resources\ReviewSingleResource;
use App\Jobs\AdminNotificationJob;
use App\Jobs\ReviewViewJob;
use App\Models\Review;
use App\Models\Sku;
use App\Models\SkuRating;
use App\Models\SkuVideo;
use App\Repositories\ReviewRepository\IReviewRepository;
use App\Services\EntityStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ReviewController extends Controller
{
    use DataProvider;

    public const LAST_LIMIT = 10;

    /**
     * @param IReviewRepository $reviewRepository
     * @param Request $request
     * @return ResourceCollection
     */
    public function my(IReviewRepository $reviewRepository, Request $request): ResourceCollection
    {
        $perPage = (int)($request->per_page ?? 10);
        $query = $reviewRepository
            ->getMyReviewsQuery()
            ->where([
                sprintf('%s.user_id', SkuRating::TABLE) => Auth::guard('api')->id(),
                sprintf('%s.status',SkuRating::TABLE) => EntityStatus::PUBLISHED,
                sprintf('%s.status',Review::TABLE) => EntityStatus::PUBLISHED,
            ]);

        $result = $this->prepareModel($request, $query)->paginate($perPage);

        return new MyReviewsCollection($result);
    }

    public function myDrafts(IReviewRepository $reviewRepository, Request $request): ResourceCollection
    {
        $query = $reviewRepository
            ->getReviewWithProductInfoQuery()
            ->where([
                sprintf('%s.user_id', SkuRating::TABLE) => Auth::guard('api')->id(),
                sprintf('%s.status',SkuRating::TABLE) => EntityStatus::PUBLISHED,
                sprintf('%s.status',Review::TABLE) => EntityStatus::DRAFT,
            ]);

        $result = $this->prepareModel($request, $query)->get();

        return new MyReviewsCollection($result);
    }

    public function myModeratedReviews(IReviewRepository $reviewRepository, Request $request): ResourceCollection
    {
        $query = $reviewRepository
            ->getReviewWithProductInfoQuery()
            ->where([
                sprintf('%s.user_id', SkuRating::TABLE) => Auth::guard('api')->id(),
                sprintf('%s.status',SkuRating::TABLE) => EntityStatus::PUBLISHED,
                sprintf('%s.status',Review::TABLE) => EntityStatus::MODERATED,
            ]);

        $result = $this->prepareModel($request, $query)->get();

        return new MyReviewsCollection($result);
    }

    public function myRejectedReviews(IReviewRepository $reviewRepository, Request $request): ResourceCollection
    {
        $query = $reviewRepository
            ->getReviewWithProductInfoQuery()
            ->where([
                sprintf('%s.user_id', SkuRating::TABLE) => Auth::guard('api')->id(),
                sprintf('%s.status',SkuRating::TABLE) => EntityStatus::PUBLISHED,
                sprintf('%s.status',Review::TABLE) => EntityStatus::REJECTED,
            ]);

        $result = $this->prepareModel($request, $query)->get();

        return new MyReviewsCollection($result);
    }

    /**
     * @param IReviewRepository $reviewRepository
     * @return LastReviewsCollection
     */
    public function last(IReviewRepository $reviewRepository): LastReviewsCollection
    {
        $result = $reviewRepository
            ->getReviewWithProductInfoQuery()
            ->orderBy('sku_ratings.created_at', 'DESC')
            ->limit(self::LAST_LIMIT)
            ->get();

        return new LastReviewsCollection($result);
    }

    /**
     * @param  IReviewRepository $reviewRepository
     * @param  int $id
     * @param  Request $request
     * @return ResourceCollection
     */
    public function bySkuId(IReviewRepository $reviewRepository, Request $request, int $id): ResourceCollection
    {
        $perPage = (int)($request->per_page ?? 10);

        $query = $reviewRepository
            ->getReviewWithCommentCountQuery()
            ->where([
                'sku_ratings.sku_id' => $id,
                'reviews.status' => 'published'
            ]);

        $result = $this->prepareModel($request, $query)->paginate($perPage);

        return new ReviewCollection($result);
    }

    /**
     * @param IReviewRepository $reviewRepository
     * @param Request $request
     * @param int $id
     * @return ReviewSingleResource|JsonResponse
     */
    public function show(IReviewRepository $reviewRepository, Request $request, int $id): ReviewSingleResource|JsonResponse
    {
        $result = $reviewRepository->getSingleReview($id);

        if (!$result) {
            return response()->json([], Response::HTTP_NOT_FOUND);
        }

        ReviewViewJob::dispatch($result->user_id, $result->id, $request->ip());


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
                'reviews.body',
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
                if ($review->body && $review->body['blocks']) {
                    foreach ($review->body['blocks'] as $block) {
                        if ($block['type'] === 'image') {
                            $common[] = [
                                'type' => 'image',
                                'url' => $block['data']['text'],
                            ];
                        }
                    }
                }
                return $common;
            },
            $info
        );


        $ratingFilter = $reviews
            ->groupBy('rating')
            ->map(fn ($group) => count($group))
            ->all();

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
            ], Response::HTTP_NOT_FOUND);
        }

        $status = $request->boolean('asDraft')
            ? EntityStatus::DRAFT
            : EntityStatus::MODERATED;

        $review = Review::query()
            ->updateOrCreate(
                [
                    'sku_rating_id' => $currentRating->id,
                    'status' => $status,
                ],
                [
                    'status' => $status,
                    'title' => $request->input('title'),
                    //'body' => mb_convert_encoding($request->input('body'), "UTF-8", "auto"),
                    'body' => $request->input('body'),
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
    public function destroy(int $id)
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
