<?php

declare(strict_types=1);


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'comments_count' => $this->comments_count,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'plus' => $this->plus,
            'minus' => $this->minus,
            'images' => $this->images ? json_decode($this->images,true) : [],
            'created_at' => $this->created_at->toDateString(),
            'user_name' => $this->anonymously === 0 ? $this->user_name : 'Имя скрыто',
            'user_avatar' => $this->anonymously === 0 ? $this->avatar : '/storage/icons/user_avatar.png'
        ];
    }
}