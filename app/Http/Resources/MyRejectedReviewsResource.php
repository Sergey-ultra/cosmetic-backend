<?php

namespace App\Http\Resources;

use App\Models\UserInfo;
use Illuminate\Http\Resources\Json\JsonResource;

class MyRejectedReviewsResource extends JsonResource
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
      

        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'comment' => $body,
            'title' => $this->title,
            'plus' => $this->plus,
            'minus' => $this->minus,
            'status' => $this->status,
            'rejected_reasons' => $this->rejectedReasons->pluck('reason')->all(),
            'created_at' => $this->created_at ? $this->created_at->toDateString() : null,
            'symbol_count' => mb_strlen($symbolCount),
            'photos_count' => $photosCount,
            'sku' => [
                'id' => $this->sku_id,
                'name' => $this->sku_name,
                'volume' => $this->volume,
                'image' => $this->sku_images ? json_decode($this->sku_images, true)[0] : [],
                'rating' => $this->common_rating,
                'product_code' => $this->product_code,
            ],
            'user_name' => $this->user_name,
            'user_avatar' => $this->avatar ?? UserInfo::DEFAULT_AVATAR,
        ];
    }
}
