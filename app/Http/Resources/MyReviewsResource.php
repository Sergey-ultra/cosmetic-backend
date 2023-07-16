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

        return [
            'sku_rating_id' => $this->sku_rating_id,
            'rating' => $this->rating,
            'sku_name' => $this->sku_name,
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
            'plus' => $this->plus,
            'minus' => $this->minus,
            'images' => $this->review_images ? json_decode($this->review_images,true) : [],
            'status' => $this->status,
            'created_at' => $this->created_at ? $this->created_at->toDateString() : null,
            'user_name' =>
                isset($this->review_id)
                    ? ($this->anonymously === 0 ? $this->user_name : 'Имя скрыто')
                    : $this->user_name,
            'user_avatar' =>
                isset($this->review_id)
                    ? $this->anonymously === 0 ? $this->avatar : UserInfo::DEFAULT_AVATAR
                    : $this->avatar
        ];
    }
}
