<?php

namespace App\Services\ReviewService;

use Illuminate\Database\Eloquent\Builder;

interface IReview
{
    public function getReviewWithProductInfoQuery(): Builder;
    public function getReviewWithCommentCountQuery(): Builder;
    public function getReviewQuery(): Builder;
}
