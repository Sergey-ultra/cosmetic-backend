<?php


namespace App\Http\Resources\Admin;


use Illuminate\Http\Resources\Json\JsonResource;

class SkuVideoResource extends JsonResource
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
            'id' => $this->id,
            'product_name' => $this->name,
            'product_link' => "/product/{$this->code}-{$this->sku_id}",
            'volume' => $this->volume,
            'description' => $this->description,
            'video' => $this->video,
            'thumbnail' => $this->thumbnail,
            'image' => $this->image,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'user_name' =>$this->user_name
        ];
    }
}
