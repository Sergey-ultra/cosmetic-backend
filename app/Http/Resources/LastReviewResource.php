<?php

namespace App\Http\Resources;

use App\Models\UserInfo;
use Illuminate\Http\Resources\Json\JsonResource;

class LastReviewResource extends JsonResource
{
    public function toArray($request)
    {
        if (is_string($this->body)) {
            $body = json_decode($this->body, true);
        } else {
            $body = $this->body;
        }
        $symbolCount = '';
        $photosCount = 0;


        foreach ($body['blocks'] as $block) {
            if ($block['type'] === 'paragraph') {
                $symbolCount .= $block['data']['text'];
            } else if ($block['type'] === 'image' && $block['data']['text']) {
                $photosCount++;
            }
        }

        //$symbolCount = mb_strlen(trim(preg_replace("/[^А-яЁёA-Za-z1-9]/g", "", $symbolCount)));
        $createdAt = null;
        if ($this->created_at) {
            if (now()->toDateString() === $this->created_at->toDateString()) {
                $createdAt = sprintf('Сегодня %s', $this->created_at->toTimeString());
            } else {
                $createdAt = $this->created_at->toDateString();
            }
        }
        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'symbol_count' => mb_strlen($symbolCount),
            'photos_count' => $photosCount,
            'views_count' => $this->views_count ?? 0,
            'likes_count' => $this->likes_count ?? 0,
            'is_recommend_percentage' => round(100 * $this->is_recommend_percentage, 2),
            'title' => $this->title,
            'plus' => $this->plus,
            'minus' => $this->minus,
            'status' => $this->status,
            'created_at' => $createdAt,
            'sku' => [
                'id' => $this->sku_id,
                'name' => $this->sku_name,
                'rating' => $this->common_rating,
                'reviews_count' => $this->sku_reviews_count,
                'product_code' => $this->product_code,
                'volume' => $this->volume,
                'image' => $this->sku_images && is_string($this->sku_images)
                    ? json_decode($this->sku_images, true)[0]
                    : [],
            ],
            'user' => [
                'id' => $this->user_id,
                'name' => $this->user_name,
                'avatar' => $this->avatar ?? UserInfo::DEFAULT_AVATAR,
            ],
        ];
    }
}
