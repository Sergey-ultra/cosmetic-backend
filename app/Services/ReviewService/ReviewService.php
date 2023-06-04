<?php

namespace App\Services\ReviewService;

use App\Models\Comment;
use App\Models\Product;
use App\Models\Review;
use App\Models\Like;
use App\Models\Sku;
use App\Models\SkuRating;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ReviewService implements IReview
{
    /** @var EloquentBuilder|Builder */
    protected EloquentBuilder|Builder $query;

    /**
     * @return EloquentBuilder|Builder
     */
    public function getQuery(): EloquentBuilder|Builder
    {
        return $this->query;
    }

    /**
     * @return Builder
     */
    public function getAdminReviewListQuery(): Builder
    {
        $this->query = DB::table(Review::TABLE)
            ->select([
                'reviews.id AS review_id',
                'sku_ratings.rating',
                'sku_ratings.id AS sku_rating_id',
                'sku_ratings.status AS rating_status',
                'reviews.body',
                'reviews.minus',
                'reviews.plus',
                'reviews.anonymously',
                'reviews.images',
                'reviews.status AS review_status'
            ])
            ->rightJoin(
                SkuRating::TABLE,
                sprintf('%s.sku_rating_id', Review::TABLE),
                '=',
                sprintf('%s.id', SkuRating::TABLE)
            );

        return $this
            ->addUserQuery()
            ->addProductInfoQuery()
            ->addUserIsNotNullCondition()
            ->getQuery();
    }

    /**
     * @return EloquentBuilder
     */
    public function getReviewWithProductInfoQuery(): EloquentBuilder
    {
        $this->query = SkuRating::query()
            ->select([
                'sku_ratings.id AS sku_rating_id',
                'sku_ratings.rating',
                'sku_ratings.created_at',
                'reviews.id AS review_id',
                'reviews.body',
                'reviews.plus',
                'reviews.minus',
                'reviews.anonymously',
                'reviews.images AS review_images',
                'reviews.status',
            ])
            ->join(Review::TABLE, function($join) {
                $join->on(
                    sprintf('%s.sku_rating_id', Review::TABLE),
                    '=',
                    sprintf('%s.id', SkuRating::TABLE)
                )
                    ->where('reviews.status', '!=', 'deleted');
            });

        return $this
            ->addViewsCountSubQuery()
            ->addProductInfoQuery()
            ->addUserQuery()
            ->addUserAdditionalInfoQuery()
            ->getQuery();
    }

    /**
     * @return EloquentBuilder
     */
    public function getReviewWithCommentCountQuery(): EloquentBuilder
    {
        return $this
            ->getReviewQuery()
            ->addCommentCountSubQuery()
            ->getQuery();
    }



    /**
     * @param int $id
     * @return EloquentBuilder|Model|null
     */
    public function getSingleReview(int $id): EloquentBuilder|Model|null
    {
        $this
            ->getReviewQuery()
            ->addProductInfoQuery()
            ->addViewsCountSubQuery()
            ->addUserReviewCountSubQuery();

        $result = $this->query
            ->withCount('likes')
            ->with(['comments' => function ($query) {
                $query
                    ->select(
                        'comments.id',
                        'comments.user_name',
                        'comments.reply_id',
                        'comments.review_id',
                        'comments.comment',
                        DB::raw('DATE(comments.created_at) AS created_at'),
                        'user_infos.avatar as user_avatar'
                    )
                    ->withCount('likes AS likes')
                    ->leftjoin('user_infos', 'comments.user_id', '=', 'user_infos.user_id')
                    ->where('comments.status', Comment::STATUS_PUBLISHED)
                    ->orderBy('created_at', 'DESC');
            }])
            ->where('reviews.id', $id)
            ->first();


        $reviewsWithSameSku = SkuRating::query()
            ->select(sprintf('%s.is_recommend', Review::TABLE))
            ->join(
                Review::TABLE,
                sprintf('%s.sku_rating_id', Review::TABLE),
                '=',
                sprintf('%s.id', SkuRating::TABLE)
            )
            ->where([
                sprintf('%s.sku_id', SkuRating::TABLE) => $result->sku_id,
                sprintf('%s.status', Review::TABLE) => Review::STATUS_PUBLISHED,
            ])
            ->get();

        $ratingsWithSameSku = SkuRating::query()
            ->selectRaw(sprintf('count(%s.rating) AS count, %s.rating', SkuRating::TABLE, SkuRating::TABLE))
            ->leftJoin(
                Review::TABLE,
                sprintf('%s.sku_rating_id', Review::TABLE),
                '=',
                sprintf('%s.id', SkuRating::TABLE)
            )
            ->where(sprintf('%s.sku_id', SkuRating::TABLE), $result->sku_id)
            ->groupBy(sprintf('%s.rating', SkuRating::TABLE))
            ->get();

        $totalRatingsCount = $ratingsWithSameSku->sum( 'count');
        $groupedRatingsWithSameSku = $ratingsWithSameSku->keyBy('rating');


        $ratingPercentage = array_map(
            static function(int $item) use ($groupedRatingsWithSameSku, $totalRatingsCount) {
                if ($existingRating = $groupedRatingsWithSameSku->get($item)) {
                    return [
                        'rating' => $item,
                        'count' => $existingRating->count,
                        'count_percent' => (int)round((100 * $existingRating->count) / $totalRatingsCount),
                    ];
                }
                return [
                    'rating' => $item,
                    'count' => 0,
                    'count_percent' => 0,
                ];
            },
            array_reverse(SkuRating::RATINGS)
        );


        $allReviews = $reviewsWithSameSku->count();

        $recommendReviewsPercent = (int)round((100 * $reviewsWithSameSku->where('is_recommend', 1)->count()) / $allReviews);

        $result->recommend_reviews_percent = $recommendReviewsPercent;
        $result->reviews_count = $allReviews;
        $result->rating_percentage = $ratingPercentage;

        return $result;
    }

    /**
     * @return $this
     */
    protected function getReviewQuery(): self
    {
        $this->query = Review::query()
            ->select([
                'reviews.id as id',
                'sku_ratings.rating',
                'reviews.title',
                'reviews.body',
                'reviews.plus',
                'reviews.minus',
                'reviews.images',
                'reviews.created_at AS created_at',
                'reviews.anonymously',
                'reviews.is_recommend',
            ])
            ->join(
                SkuRating::TABLE,
                sprintf('%s.id', SkuRating::TABLE),
                '=',
                sprintf('%s.sku_rating_id', Review::TABLE)
            );

        $this
            ->addUserQuery()
            ->addUserAdditionalInfoQuery();

        return $this;
    }

    /**
     * @return $this
     */
    protected function addUserQuery(): self
    {
        $this->query
            ->addSelect(sprintf('%s.name AS user_name', User::TABLE))
            ->join(
                User::TABLE,
                sprintf('%s.id', User::TABLE),
                '=',
                sprintf('%s.user_id', SkuRating::TABLE),
            );

        return $this;
    }

    /**
     * @return $this
     */
    protected function addUserAdditionalInfoQuery(): self
    {
        $this->query
            ->addSelect(sprintf('%s.avatar', UserInfo::TABLE))
            ->leftJoin(
                UserInfo::TABLE,
                sprintf('%s.id', User::TABLE),
                '=',
                sprintf('%s.user_id', UserInfo::TABLE)
            );

        return $this;
    }

    /**
     * @return $this
     */
    protected function addProductInfoQuery(): self
    {
        $this->query
            ->addSelect(
                'skus.id AS sku_id',
                'products.name AS sku_name',
                'products.code AS product_code',
                'skus.volume',
                'skus.rating AS common_rating',
                'skus.images AS sku_images',
            )
            ->join(
                Sku::TABLE,
                sprintf('%s.sku_id', SkuRating::TABLE),
                '=',
                sprintf('%s.id', Sku::TABLE)
            )
            ->join(
                Product::TABLE,
                sprintf('%s.product_id', Sku::TABLE),
                '=',
                sprintf('%s.id', Product::TABLE)
            );

        return $this;
    }

    /**
     * @return $this
     */
    protected function addCommentCountSubQuery(): self
    {
        $commentCountSubQuery = DB::table('comments')
            ->selectRaw('count(review_id) as comments_count, review_id')
            ->where('status', Comment::STATUS_PUBLISHED)
            ->groupBy('review_id');

        $this->query
            ->addSelect(DB::raw('IF(comments.comments_count IS NULL, 0, comments.comments_count) AS comments_count'))
            ->leftJoinSub($commentCountSubQuery, 'comments', function ($join) {
                $join->on('reviews.id', '=', 'comments.review_id');
            });

        return $this;
    }

    /**
     * @return $this
     */
    protected function addViewsCountSubQuery(): self
    {
        $viewsCountSubQuery = DB::table('review_views')
            ->select([
                DB::raw('count(ip_address) as count'),
                'review_id',
            ])
            ->groupBy('review_id');

        $this->query
            ->addSelect(DB::raw('IF(views.count IS NOT NULL, views.count, 0) AS views_count'))
            ->leftJoinSub($viewsCountSubQuery, 'views', function ($join) {
                $join->on(sprintf('%s.id', Review::TABLE), '=', 'views.review_id');
            });

        return $this;
    }

    /**
     * @return $this
     */
    protected function addUserReviewCountSubQuery(): self
    {
        $userReviewCountSubQuery = DB::table(Review::TABLE)
            ->join(
                SkuRating::TABLE,
                sprintf('%s.sku_rating_id', Review::TABLE),
                '=',
                sprintf('%s.id', SkuRating::TABLE)
            )
            ->select([
                DB::raw('count(sku_rating_id) as count'),
                'user_id',
            ])
            ->groupBy(sprintf('%s.user_id', SkuRating::TABLE));

        $this->query
            ->addSelect(DB::raw('IF(user_review_count.count IS NOT NULL, user_review_count.count, 0) AS user_review_count'))
            ->leftJoinSub($userReviewCountSubQuery, 'user_review_count', function ($join) {
            $join->on(sprintf('%s.id', User::TABLE), '=', 'user_review_count.user_id');
        });

        return $this;
    }

    protected function addIsRecommendPercentQuery(): self
    {
//        $isRecommendPercentQuery = DB::table(SkuRating::TABLE)
//            ->select([
//                DB::raw('count(sku_rating_id) as count'),
//                'sku_id',
//            ])
//            ->join(
//                Review::TABLE,
//                sprintf('%s.id', SkuRating::TABLE),
//                '=',
//                sprintf('%s.sku_rating_id', Review::TABLE)
//            )
//            ->groupBy(sprintf('%s.sku_id', SkuRating::TABLE));
//
//        $this->query
//            ->addSelect(DB::raw('IF(user_review_count.count IS NOT NULL, user_review_count.count, 0) AS user_review_count'))
//            ->leftJoinSub($isRecommendPercentQuery, 'user_review_count', function ($join) {
//                $join->on(sprintf('%s.id', User::TABLE), '=', 'user_review_count.user_id');
//            });
//
//        return $this;
    }

    /**
     * @return $this
     */
    protected function addUserIsNotNullCondition(): self
    {
        $this->query
            ->whereNotNull(sprintf('%s.user_id', SkuRating::TABLE));

        return $this;
    }
}
