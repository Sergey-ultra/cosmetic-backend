<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProvider;
use App\Http\Requests\ReviewRequest;
use App\Http\Resources\MyReviewsCollection;
use App\Http\Resources\ReviewCollection;
use App\Models\Review;
use App\Models\Sku;
use App\Models\SkuRating;
use App\Models\SkuVideo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;


class ReviewController extends Controller
{
    use DataProvider;

    /**
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function my(Request $request): ResourceCollection
    {
        $perPage = (int) ($request->per_page ?? 10);

        $query = SkuRating::select([
            'sku_ratings.id AS sku_rating_id',
            'sku_ratings.rating',
            'products.name AS sku_name',
            'products.code AS product_code',
            'skus.id AS sku_id',
            'skus.volume',
            'skus.images AS sku_images',
            'reviews.id AS review_id',
            'reviews.comment',
            'reviews.plus',
            'reviews.minus',
            'reviews.anonymously',
            'reviews.images AS review_images',
            'reviews.status',
            'sku_ratings.created_at',
            'users.name AS user_name',
            'user_infos.avatar'
        ])
            ->join('skus', 'skus.id', '=', 'sku_ratings.sku_id')
            ->join('products', 'skus.product_id', '=', 'products.id')
            ->join('users', 'users.id', '=', 'sku_ratings.user_id')
            ->leftjoin('user_infos', 'users.id', '=', 'user_infos.user_id')
            ->leftJoin('reviews', function($join) {
                $join->on('reviews.sku_rating_id', '=', 'sku_ratings.id')
                ->where('reviews.status', '!=', 'deleted');
            })
            ->where([
                'sku_ratings.user_id' => Auth::id(),
                'sku_ratings.status' => 'published'
            ])
           ;
        $result = $this->prepareModel($request, $query)->paginate($perPage);

        return new MyReviewsCollection($result);
    }

    public function additionalInfoBySkuId(int $id): JsonResponse
    {

        $videos = SkuVideo::where(['sku_id' => $id, 'status' => 'published'])->get();

        $info = $videos->reduce(
            function(array $common, SkuVideo $skuVideo): array {
                try {
                    $videoPath =  str_replace('/storage', '', $skuVideo->video);
                    $frameContents = FFMpeg::fromDisk('public')
                        ->open($videoPath)
                        ->getFrameFromSeconds(2)
                        ->export()
                        ->getFrameContents();
//                    dd(mime_content_type($frameContents));
                    $frameContents = mb_convert_encoding($frameContents, 'UTF-8', 'UTF-8');


                    if ($frameContents) {
                        $common[] = [
                            'type' => 'video',
                            'url' => $skuVideo->video,
                            'thumbnail' => $frameContents
                        ];
                    }
                } catch (\Throwable $e) {
                   throw $e;
                }

                return $common;
            },
            []
        );



        $reviews = Review::select(
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

        $ratingFilter = $reviews->groupBy('rating')->map(function($group) {
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
     * Display a listing of the resource.
     *
     * @param  int $id
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function bySkuId(int $id, Request $request): ResourceCollection
    {
        $perPage = (int) ($request->per_page ?? 10);

        $commentCountSubQuery = DB::table('comments')
            ->selectRaw('count(review_id) as comments_count, review_id')
            ->where('status', 'published')
            ->groupBy('review_id');

        $query = SkuRating::select(
            'reviews.id as id',
            'sku_ratings.rating',
             DB::raw('IF(comments.comments_count IS NULL, 0, comments.comments_count) AS comments_count'),
            'reviews.comment',
            'reviews.plus',
            'reviews.minus',
            'reviews.images',
            'reviews.created_at',
            'reviews.anonymously',
            'users.name AS user_name',
            'user_infos.avatar'
        )
            ->join('reviews', 'sku_ratings.id', '=', 'reviews.sku_rating_id')
            ->join('users', 'users.id', '=', 'sku_ratings.user_id')
            ->leftjoin('user_infos', 'users.id', '=', 'user_infos.user_id')
            ->leftJoinSub($commentCountSubQuery, 'comments', function ($join) {
                $join->on('reviews.id', '=', 'comments.review_id');
            })
            ->where([
                'sku_ratings.sku_id' => $id,
                'reviews.status' => 'published'
            ]);

        $result = $this->prepareModel($request, $query)->paginate($perPage);

        //return response()->json(['data' => $result]);

        return new ReviewCollection($result);
    }

    /**
     * check Existing Review.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkExistingReview(Request $request): JsonResponse
    {
        $conditions[] = ['sku_ratings.sku_id', '=', $request->sku_id];

        $user = Auth::guard('sanctum')->user();
        if (isset($user)) {
           $conditions[] = ['sku_ratings.user_id' , '=', $user->id];
        } else {
            $conditions[] = ['sku_ratings.ip_address', '=', $request->ip()];
        }
        //$conditions[] = ['reviews.status', '<>', 'deleted'];

        $existingReview = Review::select([
            'sku_ratings.rating',
            'reviews.id',
            'reviews.comment',
            'reviews.plus',
            'reviews.minus',
            'reviews.images',
            'reviews.status',
            'reviews.anonymously',
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


        $currentRating = SkuRating::where([
            'sku_id' => $skuId,
            'user_id' => Auth::guard('sanctum')->user()->id
        ])->first();

        $currentSku = Sku::find($skuId);

        if (!$currentRating  || !$currentSku) {
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
                'comment' => $request->comment,
                'plus' => $request->plus,
                'minus' => $request->minus,
                'images' => $request->images,
                'anonymously' => $request->anonymously ?? 0
            ]
        );

        return response()->json([
            'data' => [
                'status' => 'success',
                'data' => $review
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
