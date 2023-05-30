<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ArticleComment extends Model
{
    protected $table = 'article_comments';

    protected $fillable = ['comment', 'article_id', 'user_id', 'user_name', 'user_avatar', 'reply_id', 'status'];

    protected $casts = [
        'created_at'  => 'date:Y-m-d',
    ];

    /**
     * @return BelongsTo
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * @return MorphMany
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

}
