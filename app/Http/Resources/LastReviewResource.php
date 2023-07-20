<?php

namespace App\Http\Resources;

use App\Models\UserInfo;
use Illuminate\Http\Resources\Json\JsonResource;

class LastReviewResource extends JsonResource
{
    public function toArray($request)
    {
        $body = json_decode($this->body, true);
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
            'sku_rating_id' => $this->sku_rating_id,
            'rating' => $this->rating,
            'sku_name' => $this->sku_name,
            'common_rating' => $this->common_rating,
            'product_code' => $this->product_code,
            'symbol_count' => mb_strlen($symbolCount),
            'photos_count' => $photosCount,
            'views_count' => $this->views_count ?? 0,
            'balance' => $this->balance ? $this->balance / 1000 : 0,
            'bonus' => $this->bonus ? $this->bonus / 1000 : 0,
            'likes_count' => $this->likes_count ?? 0,
            'sku_id' => $this->sku_id,
            'volume' => $this->volume,
            'sku_image' => $this->sku_images ? json_decode($this->sku_images, true)[0] : [],
            'review_id' => $this->review_id,
            'comment' => $body,
            'title' => $this->title,
            'plus' => $this->plus,
            'minus' => $this->minus,
            //'images' => $this->review_images ? json_decode($this->review_images,true) : [],
            'status' => $this->status,
            'created_at' => $createdAt,
            'user_name' => $this->user_name,
            'user_avatar' => $this->avatar ?? UserInfo::DEFAULT_AVATAR,
        ];
    }
}
