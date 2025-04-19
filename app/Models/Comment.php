<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Comment extends Model
{
    public const TABLE = 'comments';

    protected $table = self::TABLE;

    protected $fillable = ['comment', 'review_id', 'user_id', 'reply_id', 'status'];
    protected $casts = [
        'created_at'  => 'date:Y-m-d',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return MorphMany
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }


    /**
     * @param $reviewId
     * @return void
     */
    public function nestedComments($reviewId)
    {
        $comments = self::select(
            'comments.id as id',
            'comments.user_name as user_name',
            'comments.reply_id as reply_id',
            'comments.review_id as review_id',
            'comments.comment as comment',
            'comments.created_at as created_at',
            'user_infos.avatar as user_avatar'

        )
            ->leftjoin('user_infos', 'comments.user_id', '=', 'user_infos.user_id')
            ->where(['review_id' => $reviewId])
            ->orderBy('created_at', 'DESC')
            ->get();
    }



    public static  function getNestedComments($reviewId)
    {

        $comments = self::select(
            'comments.id as id',
            'comments.user_name as user_name',
            'comments.reply_id as reply_id',
            'comments.review_id as review_id',
            'comments.comment as comment',
            'comments.created_at as created_at',
            'user_infos.avatar as user_avatar'

        )
            ->leftjoin('user_infos', 'comments.user_id', '=', 'user_infos.user_id')
            ->where(['review_id' => $reviewId, 'reply_id' => NULL])
            ->orderBy('created_at', 'DESC')
            ->get();

        $result = [];

        foreach($comments as $comment){
            $commentWithChilds = [];
            $commentWithChilds[] =  $comment ;

            $childComments = self::getChildComment($comment->id);
            if ($childComments) {
                $commentWithChilds[] =  $childComments;
            }

            $result[] =  $commentWithChilds;
        }

        return  $result ;
    }



    private static function getChildComment($id)
    {
        $subComments = self::select(
            'comments.id as id',
            'comments.user_name as user_name',
            'comments.reply_id as reply_id',
            'comments.review_id as review_id',
            'comments.comment as comment',
            'comments.created_at as created_at',
            'user_infos.avatar as user_avatar'

        )
            ->leftjoin('user_infos', 'comments.user_id', '=', 'user_infos.user_id')
            ->where('reply_id', $id)
            ->orderBy('created_at', 'DESC')
            ->get();

        $list = [];
        foreach($subComments as $subComment){
            $subCommentWithChildsOneLevel = [];
            $subCommentWithChildsOneLevel[] =  $subComment ;

            $childComments = self::getChildComment($subComment->id);
            if ($childComments){
                $subCommentWithChildsOneLevel[] = $childComments;
            }

            $list[] =  $subCommentWithChildsOneLevel;
        }

        return  $list;
    }
}
