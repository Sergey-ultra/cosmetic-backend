<?php

declare(strict_types=1);


namespace App\Http\Resources;


use App\Models\UserInfo;
use Illuminate\Http\Resources\Json\JsonResource;

class MyReviewsResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
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

        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'comment' => $body,
            'title' => $this->title,
            'plus' => $this->plus,
            'minus' => $this->minus,
            'status' => $this->status,
            'created_at' => $this->created_at ? $this->created_at->toDateString() : null,
            'symbol_count' => mb_strlen($symbolCount),
            'photos_count' => $photosCount,
            'views_count' => $this->views_count ?? 0,
            'balance' => $this->bonus ? $this->bonus / 1000 : 0,
            'likes_count' => $this->likes_count ?? 0,
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
