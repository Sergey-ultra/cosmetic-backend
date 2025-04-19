<?php

namespace App\Repositories\ReviewRepository;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;

interface IReviewRepository
{
    public function getAdminReviewListQuery(): \Illuminate\Database\Query\Builder;
    public function getLastReviewQuery(): EloquentBuilder;
    public function getReviewWithProductInfoQuery(): Builder;
    public function getReviewWithCommentCountQuery(): Builder;
    public function getSingleReview(int $id): Builder|Model|null;
}
