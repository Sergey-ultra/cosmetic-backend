<?php


namespace App\Http\Resources;


use App\Models\Like;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleWithTagsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
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

        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'category_name' => $this->category_name,
            'category_color' => $this->category_color,
            'title' => $this->title,
            'slug' => $this->slug,
            'preview' => $this->preview,
            'image' => $this->image,
            'tags' => $tags,
            'likes' => $this->likes->count(),
            'is_vote' => $this->likes
                ->contains(function (Like $like) use ($request) {
                    return $like->ip_address = $request->ip();
                }),
            'user_name' => $this->user_name,
            'user_avatar' => $this->user_avatar,
            'views_count' => $this->views_count,
            'comments_count' => $this->comments_count,
            'created_at' => $this->created_at->format('d-m-Y'),
        ];
    }
}
