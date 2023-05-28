<?php

namespace App\Http\Resources;

use App\Services\TreeService\TreeService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ReviewSingleResource extends JsonResource
{
    public function toArray($request): array
    {
        $comments = [];
        if ($this->comments) {
            $arrayOfComments = $this->comments->toArray();
            $comments = (new TreeService())->buildTree($arrayOfComments, 'reply_id');
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'rating' => $this->rating,
            'views_count' => $this->views_count,
            'plus' => $this->plus,
            'minus' => $this->minus,
            'body' => $this->body,
            'images' => $this->images,
            'created_at' => $this->created_at->toDateString(),
            'user_name' => $this->anonymously === 0
                ? $this->user_name
                : 'Имя скрыто',
            'user_avatar' => $this->anonymously === 0
                ? $this->avatar ?? '/storage/icons/user_avatar.png'
                : '/storage/icons/user_avatar.png',
            'comments_count' => $this->comments->count(),
            'comments' => $comments,
        ];
    }
}
