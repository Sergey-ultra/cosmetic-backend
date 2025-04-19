<?php
declare(strict_types=1);


namespace App\Http\Resources\Admin;


use Illuminate\Http\Resources\Json\JsonResource;

class ReviewOneResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'title' => $this->title,
            'comment' => $this->comment,
            'plus' => $this->plus,
            'minus' => $this->minus,
            'images' => $this->images,
            'status' => $this->status,
        ];
    }
}
