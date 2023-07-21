<?php

namespace App\Repositories\ReviewRepository;

use App\Models\Comment;
use App\Models\Product;
use App\Models\Review;
use App\Models\Sku;
use App\Models\User;
use App\Models\UserBalanceAccrual;
use App\Models\UserInfo;
use App\Services\EntityStatus;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ReviewRepository implements IReviewRepository
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
                'id AS review_id',
                'rating',
                'title',
                'body',
                'minus',
                'plus',
                'images',
                'created_at',
                'status AS review_status'
            ]);

        return $this
            ->addViewsCountSubQuery()
            ->addLikesCountSubQuery()
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
        return $this->setReviewWithProductInfoQuery()->getQuery();
    }

    /**
     * @return EloquentBuilder
     */
    public function getMyReviewsQuery(): EloquentBuilder
    {
        return $this->setReviewWithProductInfoQuery()
            ->addBalanceAccruals()
            ->addLikesCountSubQuery()
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
        $result = $this->getSingleReviewRawModel($id);


        $reviewsWithSameSku = Review::query()
            ->select('is_recommend')
            ->where([
               'sku_id'  => $result->sku_id,
               'status'  => EntityStatus::PUBLISHED,
            ])
            ->get();


        $allReviews = $reviewsWithSameSku->count();

        if ($allReviews > 0) {
            $recommendReviewsPercent = (int)round((100 * $reviewsWithSameSku->where('is_recommend', 1)->count()) / $allReviews);
        }



        $result->recommend_reviews_percent = $recommendReviewsPercent ?? 0;
        $result->reviews_count = $allReviews;
        $result->rating_percentage = $this->getRatingsPercentageWithSameSku($result->sku_id);

        return $result;
    }

    /**
     * @return self
     */
    protected function setReviewWithProductInfoQuery(): self
    {
        $this->query = Review::query()
            ->select(['rating', 'created_at', 'id AS review_id', 'title', 'body', 'plus', 'minus', 'status'])
            ->where('status', '!=', 'deleted');


        return $this
            ->addViewsCountSubQuery()
            ->addProductInfoQuery()
            ->addUserQuery()
            ->addUserAdditionalInfoQuery();
    }

    protected function getSingleReviewRawModel(int $id): Model|null
    {
        $this
            ->getReviewQuery()
            ->addProductInfoQuery()
            ->addViewsCountSubQuery()
            ->addUserReviewCountSubQuery();

        return $this->query
            ->with('likes')
            ->with(['comments' => function ($query) {
                $query
                    ->select([
                        'comments.id',
                        sprintf('%s.name AS user_name', User::TABLE),
                        'comments.reply_id',
                        'comments.review_id',
                        'comments.comment',
                        DB::raw(sprintf('DATE(%s.created_at) AS created_at', Comment::TABLE)),
                        sprintf('%s.avatar as user_avatar', UserInfo::TABLE),
                    ])
                    ->with('likes')
                    ->leftJoin(
                        User::TABLE,
                        sprintf('%s.user_id', Comment::TABLE),
                        '=',
                        sprintf('%s.id', User::TABLE)
                    )
                    ->leftjoin(
                        UserInfo::TABLE,
                        sprintf('%s.user_id', Comment::TABLE),
                        '=',
                        sprintf('%s.user_id', UserInfo::TABLE)
                    )
                    ->where(sprintf('%s.status', Comment::TABLE), EntityStatus::PUBLISHED)
                    ->orderBy('created_at', 'DESC');
            }])
            ->where('reviews.id', $id)
            ->first();

    }

    /**
     * @param int $skuId
     * @return array
     */
    protected function getRatingsPercentageWithSameSku(int $skuId): array
    {
        $ratingsWithSameSku = Review::query()
            ->selectRaw('count(rating) AS count, rating')
            ->where('sku_id',  $skuId)
            ->groupBy('rating')
            ->get();

        $totalRatingsCount = $ratingsWithSameSku->sum('count');
        $groupedRatingsWithSameSku = $ratingsWithSameSku->keyBy('rating');


        return array_map(
            static function (int $item) use ($groupedRatingsWithSameSku, $totalRatingsCount) {
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
            array_reverse(Review::RATINGS)
        );
    }

    /**
     * @return $this
     */
    protected function getReviewQuery(): self
    {
        $this->query = Review::query()
            ->select([
                'id',
                'rating',
                'user_id',
                'title',
                'body',
                'plus',
                'minus',
                'images',
                'created_at',
                'is_recommend',
            ]);

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
            ->addSelect(
                sprintf('%s.name AS user_name', User::TABLE),
                sprintf('%s.id AS user_id', User::TABLE),
            )
            ->join(
                User::TABLE,
                sprintf('%s.id', User::TABLE),
                '=',
                sprintf('%s.user_id', Review::TABLE),
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
                sprintf('%s.sku_id', Review::TABLE),
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
            ->where('status', EntityStatus::PUBLISHED)
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

    protected function addBalanceAccruals(): self
    {
        $sumViewsAccrualsSubQuery = DB::table(UserBalanceAccrual::TABLE)
            ->select([
                DB::raw('sum(accrual) as sum'),
                'review_id',
            ])
            ->where('type', UserBalanceAccrual::VIEW_TYPE)
            ->groupBy('review_id');

        $this->query
            ->addSelect([
                DB::raw('IF(balance.sum IS NOT NULL, balance.sum, 0) AS balance'),
                DB::raw('IF(bonus.accrual IS NOT NULL, bonus.accrual, 0) AS bonus'),
            ])
            ->leftJoinSub($sumViewsAccrualsSubQuery, 'balance', function ($join) {
                $join->on(sprintf('%s.id', Review::TABLE), '=', 'balance.review_id');
            })
            ->leftJoin(sprintf('%s AS bonus', UserBalanceAccrual::TABLE), function ($join) {
                $join->on(sprintf('%s.id', Review::TABLE), '=', 'bonus.review_id')
                    ->where('bonus.type', UserBalanceAccrual::VIEW_BONUS)
                ;
            });

        return $this;
    }

    protected function addLikesCountSubQuery(): self
    {
        $viewsCountSubQuery = DB::table('likes')
            ->select([
                DB::raw('count(ip_address) as count'),
                'likeable_id',
                'likeable_type',
            ])
            ->groupBy('likeable_id', 'likeable_type');

        $this->query
            ->addSelect(DB::raw('IF(likes.count IS NOT NULL, likes.count, 0) AS likes_count'))
            ->leftJoinSub($viewsCountSubQuery, 'likes', function ($join) {
                $join
                    ->on(sprintf('%s.id', Review::TABLE), '=', 'likes.likeable_id')
                    ->where('likes.likeable_type', Review::class);
            });

        return $this;
    }


    /**
     * @return $this
     */
    protected function addUserReviewCountSubQuery(): self
    {
        $userReviewCountSubQuery = DB::table(Review::TABLE)
            ->selectRaw('count(sku_rating_id) as count, user_id')
            ->groupBy('user_id');

        $this->query
            ->addSelect(DB::raw('IF(user_review_count.count IS NOT NULL, user_review_count.count, 0) AS user_review_count'))
            ->leftJoinSub($userReviewCountSubQuery, 'user_review_count', function ($join) {
                $join->on(sprintf('%s.id', User::TABLE), '=', 'user_review_count.user_id');
            });

        return $this;
    }


    /**
     * @return $this
     */
    protected function addUserIsNotNullCondition(): self
    {
        $this->query->whereNotNull('user_id');

        return $this;
    }
}
