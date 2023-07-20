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
    public function toArray($request): array
    {
        $body = '';
        $images = [];
        foreach ($this->body['blocks'] as $block) {
            if ($block['type'] === 'paragraph') {
                $body .= $block['data']['text'];
            } elseif ($block['type'] === 'image') {
                $images[] = $block['data']['text'];
            }
        }

        return [
            'id' => $this->id,
            'comments_count' => $this->comments_count,
            'rating' => $this->rating,
            'title' => $this->title,
            'body' => Str::substr($body, 0, 400) . (Str::length($body) > 400 ? '...' : ''),
            'plus' => $this->plus,
            'minus' => $this->minus,
            'images' => $images,
            'created_at' => $this->created_at->toDateString(),
            'user_id' => $this->user_id,
            'user_name' => $this->user_name,
            'user_avatar' => $this->avatar ??  UserInfo::DEFAULT_AVATAR,
        ];
    }
}
