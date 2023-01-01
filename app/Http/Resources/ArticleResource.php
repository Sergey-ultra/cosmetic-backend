<?php

namespace App\Http\Resources;

use App\Services\TransformImagePathService\TransformImagePathService;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $transformImagePathService = new TransformImagePathService();
        return [
            'id' => $this->id,
            'category_name' => $this->category_name,
            'category_color' => $this->category_color,
            'title' => $this->title,
            'slug' => $this->slug,
            'preview' => $this->preview,
            'image' => $transformImagePathService->getDestinationPath($this->image, 'medium'),
            'user_name' => $this->user_name,
            'user_avatar' => $this->user_avatar,
            'views_count' => $this->views_count,
            'created_at' => $this->created_at->format('d-m-Y')
        ];
    }
}
