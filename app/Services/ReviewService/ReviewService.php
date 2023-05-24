<?php

namespace App\Services\ReviewService;

use App\Models\Review;
use App\Models\SkuRating;
use Illuminate\Database\Eloquent\Builder;
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
            ->leftJoinSub($commentCountSubQuery, 'comments', function ($join) {
                $join->on('reviews.id', '=', 'comments.review_id');
            });
    }

    public function getReviewQuery(): Builder
    {
        return Review::query()
            ->join('sku_ratings', 'sku_ratings.id', '=', 'reviews.sku_rating_id')
            ->join('users', 'users.id', '=', 'sku_ratings.user_id')
            ->leftjoin('user_infos', 'users.id', '=', 'user_infos.user_id');
    }

    public function getSelectForCommonQuery(): array
    {
        return [
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
        ];
    }
}
