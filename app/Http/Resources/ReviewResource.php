<?php

declare(strict_types=1);


namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ReviewResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'comments_count' => $this->comments_count,
            'rating' => $this->rating,
            'title' => $this->title,
            'body' => Str::substr($this->body, 0, 400) . (Str::length($this->body) > 400 ? '...' : ''),
            'plus' => $this->plus,
            'minus' => $this->minus,
            'images' => $this->images,
            'created_at' => $this->created_at->toDateString(),
            'user_name' => $this->anonymously === 0
                ? $this->user_name
                : 'Имя скрыто',
            'user_avatar' => $this->anonymously === 0
                ? $this->avatar ?? '/storage/icons/user_avatar.png'
                : '/storage/icons/user_avatar.png',
        ];
    }
}
