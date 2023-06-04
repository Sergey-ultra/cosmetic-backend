<?php

namespace App\Http\Resources;

use App\Models\UserInfo;
use App\Services\TreeService\TreeService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ReviewSingleResource extends JsonResource
{
    public function toArray($request): array
    {
        $comments = [];
        if ($this->comments) {
            $arrayOfComments = $this->comments->toArray();
            $comments = (new TreeService())->buildTree($arrayOfComments, 'reply_id');
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'common_rating' => round($this->common_rating, 1),
            'rating' => $this->rating,
            'views_count' => $this->views_count,
            'likes' => $this->likes_count,
            'recommend_reviews_percent' => $this->recommend_reviews_percent,
            'reviews_count' => $this->reviews_count,
            'rating_percentage' => $this->rating_percentage,

            'sku_name' => "{$this->sku_name} {$this->volume}",
            'sku_code' => "{$this->product_code}-{$this->sku_id}",
            'sku_image' => $this->sku_images ? json_decode($this->sku_images, true)[0] : [],

            'plus' => $this->plus,
            'minus' => $this->minus,
            'body' => $this->body,
            'is_recommend' => $this->is_recommend,
            'images' => $this->images,
            'created_at' => $this->created_at->toDateString(),
            'user_name' => $this->anonymously === 0
                ? $this->user_name
                : 'Имя скрыто',
            'user_avatar' => $this->anonymously === 0
                ? $this->avatar ?? UserInfo::DEFAULT_AVATAR
                : UserInfo::DEFAULT_AVATAR,
            'user_review_count' => $this->user_review_count,
            'comments_count' => $this->comments->count(),
            'comments' => $comments,
        ];
    }
}
