<?php

namespace App\Services\ReviewService;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;

interface IReview
{
    public function getAdminReviewListQuery(): \Illuminate\Database\Query\Builder;
    public function getReviewWithProductInfoQuery(): Builder;
    public function getReviewWithCommentCountQuery(): Builder;
    public function getSingleReview(int $id): EloquentBuilder|Model|null;
}
