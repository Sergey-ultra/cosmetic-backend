<?php

namespace App\Http\Resources;

use App\Models\ArticleComment;
use App\Models\Comment;
use App\Models\Like;
use App\Services\TreeService\TreeService;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentsTreeResource extends JsonResource
{
    public function toArray($request): array
    {
        $comments = [];
        if ($this) {
            $arrayOfComments = $this
                ->map(function(ArticleComment|Comment $item) use($request) {
                    return [
                        'id' => $item->id,
                        'user_name' => $item->user_name,
                        'reply_id' => $item->reply_id,
                        'comment' => $item->comment,
                        'created_at' =>$item->created_at,
                        'user_avatar' => $item->user_avatar,
                        'likes' => $item->likes->count(),
                        'is_vote' => $item->likes->contains(function(Like $like) use($request): bool {
                            return $like->ip_address === $request->ip();
                        }),
                    ];
                })
                ->toArray();
            $comments = (new TreeService())->buildTree($arrayOfComments, 'reply_id');
        }

        return $comments;
    }
}
