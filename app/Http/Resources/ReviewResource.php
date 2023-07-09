<?php

declare(strict_types=1);


namespace App\Http\Resources;


use App\Models\UserInfo;
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

        $body = array_reduce(
            $this->body['blocks'],
            function ($acc, $block) {
                if ($block['type'] === 'paragraph') {
                    $acc .= $block['data']['text'];
                }
                return $acc;
            },
            ''
        );

        return [
            'id' => $this->id,
            'comments_count' => $this->comments_count,
            'rating' => $this->rating,
            'title' => $this->title,
            'body' => Str::substr($body, 0, 400) . (Str::length($body) > 400 ? '...' : ''),
            'plus' => $this->plus,
            'minus' => $this->minus,
            'images' => $this->images,
            'created_at' => $this->created_at->toDateString(),
            'user_name' => $this->anonymously === 0
                ? $this->user_name
                : 'Имя скрыто',
            'user_avatar' => $this->anonymously === 0
                ? $this->avatar ??  UserInfo::DEFAULT_AVATAR
                :  UserInfo::DEFAULT_AVATAR,
        ];
    }
}
