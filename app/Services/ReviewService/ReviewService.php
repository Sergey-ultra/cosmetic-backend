<?php

namespace App\Services\ReviewService;

use App\Models\Review;
use App\Models\Like;
use App\Models\SkuRating;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReviewService implements IReview
{
    public function getReviewWithProductInfoQuery(): Builder
    {
        return SkuRating::query()->select([
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
            ->leftjoin('user_infos', 'users.id', '=', 'user_infos.user_id');
    }

    public function getReviewWithCommentCountQuery(): Builder
    {
        $commentCountSubQuery = DB::table('comments')
            ->selectRaw('count(review_id) as comments_count, review_id')
            ->where('status', 'published')
            ->groupBy('review_id');

        return $this
            ->getReviewQuery()
            ->addSelect(DB::raw('IF(comments.comments_count IS NULL, 0, comments.comments_count) AS comments_count'))
            ->leftJoinSub($commentCountSubQuery, 'comments', function ($join) {
                $join->on('reviews.id', '=', 'comments.review_id');
            });
    }

    public function getReviewQuery(): Builder
    {
        return Review::query()
            ->select([
                'reviews.id as id',
                'sku_ratings.rating',
                'reviews.title',
                'reviews.comment AS body',
                'reviews.plus',
                'reviews.minus',
                'reviews.images',
                'reviews.created_at AS created_at',
                'reviews.anonymously',
                'users.name AS user_name',
                'user_infos.avatar'
            ])
            ->join('sku_ratings', 'sku_ratings.id', '=', 'reviews.sku_rating_id')
            ->join('users', 'users.id', '=', 'sku_ratings.user_id')
            ->leftjoin('user_infos', 'users.id', '=', 'user_infos.user_id');
    }

    /**
     * @param int $id
     * @return Builder|Model|null
     */
    public function getSingleReview(int $id): Builder|Model|null
    {
        $viewsCountSubQuery = DB::table('review_views')
            ->select([DB::raw('count(ip_address) as count'), 'review_id'])
            ->groupBy('review_id');

        $likesCountSubQuery = DB::table(Like::TABLE)
            ->select([DB::raw('count(plus_ip_address) as count'), 'review_id'])
            ->whereNotNull('plus_ip_address')
            ->groupBy('review_id');

        return $this
            ->getReviewQuery()
            ->addSelect([
                DB::raw('IF(views.count IS NOT NULL, views.count, 0) AS views_count'),
                DB::raw('IF(likes.count IS NOT NULL, likes.count, 0) AS likes'),
            ])
            ->with(['comments' => function ($query) {
                $query
                    ->select(
                        'comments.id',
                        'comments.user_name',
                        'comments.reply_id',
                        'comments.review_id',
                        'comments.comment',
                        DB::raw('DATE(comments.created_at) AS created_at'),
                        DB::raw('0 as likes'),
                        'user_infos.avatar as user_avatar'
                    )
                    ->leftjoin('user_infos', 'comments.user_id', '=', 'user_infos.user_id')
                    ->where('comments.status', 'published')
                    ->orderBy('created_at', 'DESC');
            }])
            ->leftJoinSub($viewsCountSubQuery, 'views', function ($join) {
                $join->on('reviews.id', '=', 'views.review_id');
            })
            ->leftJoinSub($likesCountSubQuery, 'likes', function ($join) {
                $join->on('reviews.id', '=', 'likes.review_id');
            })
            ->where('reviews.id', $id)
            ->first();
    }
}
