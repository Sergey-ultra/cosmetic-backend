<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;


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
        $tag_ids = [];
        if ($this->tags) {
            $tag_ids = $this->tags->map(function ($item, $key) {
                return $item->pivot->tag_id;
            });
        }


        return [
            'id' => $this->id,
            'title' => $this->title,
            'preview' => $this->preview,
            'slug' => $this->slug,
            'body' => $this->body,
            'article_category_id' => $this->article_category_id,
            'image' => $this->image,
            'status' => $this->status,
            'tag_ids' => $tag_ids
        ];
    }
}
