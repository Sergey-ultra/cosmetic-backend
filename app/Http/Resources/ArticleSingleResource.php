<?php

declare(strict_types=1);


namespace App\Http\Resources;


use App\Services\TreeService\TreeInterface;
use App\Services\TreeService\TreeService;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleSingleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $tags = [];

        if ($this->tags) {
            $tags = $this->tags->map(function ($item, $key) {
                return $item->tag;
            });
        }
        $comments = [];
        if ($this->comments) {
            $arrayOfComments = $this->comments->toArray();
            $comments = (new TreeService())->buildTree($arrayOfComments, 'reply_id');
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'preview' => $this->preview,
            'image' => $this->image,
            'body' => $this->body,
            'category_id' => $this->category_id,
            'category_name' => $this->category_name,
            'category_color' => $this->category_color,
            'comments' => $comments,
            'created_at' => $this->created_at->format('d-m-Y'),
            'user_name' => $this->user_name,
            'user_avatar' => $this->user_avatar,
            'views_count' => $this->views_count,
            'tags' => $tags
        ];
    }
}
