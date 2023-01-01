<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class MyQuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'sku_name' => $this->sku_name,
            'product_code' => $this->product_code,
            'sku_id' => $this->sku_id,
            'volume' => $this->volume,
            'sku_image' => $this->sku_images ? json_decode($this->sku_images, true)[0] : [],
            'body' => $this->body,
            'status' => $this->status,
            'created_at' => $this->created_at ? $this->created_at->toDateString() : null,
            'user_name' => $this->user_name,
            'user_avatar' => $this->avatar ?? '/storage/icons/user_avatar.png'

        ];
    }
}
