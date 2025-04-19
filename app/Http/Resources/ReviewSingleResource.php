<?php

namespace App\Http\Resources;

use App\Models\Comment;
use App\Models\Like;
use App\Models\UserInfo;
use App\Services\TreeService\TreeService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewSingleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'common_rating' => round($this->common_rating, 1),
            'rating' => $this->rating,
            'views_count' => $this->views_count,
            'likes' => $this->likes->count(),
            'is_vote' => $this->likes
                ->contains(function (Like $like) use ($request) {
                    return $like->ip_address = $request->ip();
                }),
            'recommend_reviews_percent' => $this->recommend_reviews_percent,
            'reviews_count' => $this->reviews_count,
            'rating_percentage' => $this->rating_percentage,

            'sku_name' => "{$this->sku_name} {$this->volume}",
            'sku_code' => "{$this->product_code}-{$this->sku_id}",
            'sku_image' => $this->sku_images
                ? json_decode($this->sku_images, true)[0]
                : null,

            'plus' => $this->plus,
            'minus' => $this->minus,
            'body' => $this->body,
            'is_recommend' => $this->is_recommend,
            'created_at' => $this->created_at->toDateString(),
            'user_id' => $this->user_id,
            'user_name' => $this->user_name,
            'user_avatar' => $this->avatar ?? UserInfo::DEFAULT_AVATAR,
            'user_review_count' => $this->user_review_count,
            'comments_count' => $this->comments->count(),
            'comments' => new CommentsTreeResource($this->comments),
        ];
    }
}
