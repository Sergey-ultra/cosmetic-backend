<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;

/**
 * @property-read Tag[] $tags
 */
class Article extends Model
{
    public const TABLE = 'articles';

    protected $table = self::TABLE;

    protected $fillable = [
        'title',
        'slug',
        'preview',
        'status',
        'body',
        'image',
        'article_category_id',
        'user_id'
    ];

    protected $casts = [
        'created_at'  => 'date:Y-m-d',
    ];

    /**
     * @return BelongsToMany
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(ArticleComment::class);
    }

    /**
     * @return MorphMany
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }


    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeWithFullInfo($query)
    {
        $viewsCountSubQuery = DB::table('article_views')
            ->select([DB::raw('count(ip_address) as count'), 'article_id'])
            ->groupBy('article_id');


        $query->select(
            'articles.id',
            'articles.title',
            'articles.slug',
            'articles.preview',
            'articles.image',
            'articles.status',
            'articles.user_id',
            'articles.created_at',
            'users.name AS user_name',
            'user_infos.avatar AS user_avatar',
            DB::raw('IF(article_views.count IS NOT NULL, article_views.count, 0) AS views_count'),
            'article_categories.name AS category_name',
            'article_categories.color AS category_color',
        )
            ->join('users', 'articles.user_id', '=', 'users.id')
            ->leftJoin('user_infos', 'users.id', '=', 'user_infos.user_id')
            ->leftJoin('article_categories', 'article_categories.id', '=', 'articles.article_category_id')
            ->leftJoinSub($viewsCountSubQuery, 'article_views', function ($join) {
                $join->on('articles.id', '=', 'article_views.article_id');
            });

    }

    public function scopeWithTags($query): void
    {
        $viewsCountSubQuery = DB::table('article_views')
            ->select([DB::raw('count(ip_address) as count'), 'article_id'])
            ->groupBy('article_id');

        $commentsCountSubQuery = DB::table('article_comments')
            ->select([DB::raw('count(article_id) as count'), 'article_id'])
            ->where('status', 'published')
            ->groupBy('article_id');


        $query->select(
            'articles.id',
            'articles.title',
            'articles.slug',
            'articles.preview',
            'articles.body',
            'articles.image',
            'articles.created_at',
            'users.name AS user_name',
            'user_infos.avatar AS user_avatar',
            DB::raw('IF(article_views.count IS NOT NULL, article_views.count, 0) AS views_count'),
            DB::raw('IF(article_comments.count IS NOT NULL, article_comments.count, 0) AS comments_count'),
            'article_categories.id AS category_id',
            'article_categories.name AS category_name',
            'article_categories.color AS category_color',
        )
            ->with(['tags', 'likes'])
            ->join('users', 'articles.user_id', '=', 'users.id')
            ->leftJoin('user_infos', 'users.id', '=', 'user_infos.user_id')
            ->leftJoin('article_categories', 'article_categories.id', '=', 'articles.article_category_id')
            ->leftJoinSub($viewsCountSubQuery, 'article_views', function ($join) {
                $join->on('articles.id', '=', 'article_views.article_id');
            })
            ->leftJoinSub($commentsCountSubQuery, 'article_comments', function ($join) {
                $join->on('articles.id', '=', 'article_comments.article_id');
            })
        ;
    }

    public function scopeAdditionalInfoWithTagsWithJoinToTags($query): void
    {
        $this->scopeWithTags($query);
//        $viewsCountSubQuery = DB::table('article_views')
//            ->select([DB::raw('count(ip_address) as count'), 'article_id'])
//            ->groupBy('article_id');
//
//        $commentsCountSubQuery = DB::table('article_comments')
//            ->select([DB::raw('count(article_id) as count'), 'article_id'])
//            ->where('status', 'published')
//            ->groupBy('article_id');

        $tagSubQuery = DB::table('tags')
            ->select('tags.tag', 'tags.id', 'article_tag.article_id')
            ->join('article_tag','tags.id', '=', 'article_tag.tag_id');

//        $query->select(
//            'articles.id',
//            'articles.title',
//            'articles.slug',
//            'articles.preview',
//            'articles.body',
//            'articles.image',
//            'articles.created_at',
//            'users.name AS user_name',
//            'user_infos.avatar AS user_avatar',
//            DB::raw('IF(article_views.count IS NOT NULL, article_views.count, 0) AS views_count'),
//            DB::raw('IF(article_comments.count IS NOT NULL, article_comments.count, 0) AS comments_count'),
//            'article_categories.name AS category_name',
//            'article_categories.color AS category_color',
//        )
//            ->with('tags')
//            ->joinSub($tagSubQuery, 'tags', function($join) {
//                $join->on('articles.id', '=', 'tags.article_id');
//            })
//            ->join('users', 'articles.user_id', '=', 'users.id')
//            ->leftJoin('user_infos', 'users.id', '=', 'user_infos.user_id')
//            ->leftJoin('article_categories', 'article_categories.id', '=', 'articles.article_category_id')
//            ->leftJoinSub($viewsCountSubQuery, 'article_views', function ($join) {
//                $join->on('articles.id', '=', 'article_views.article_id');
//            })
//            ->leftJoinSub($commentsCountSubQuery, 'article_comments', function ($join) {
//                $join->on('articles.id', '=', 'article_comments.article_id');
//            });
        $query->joinSub($tagSubQuery, 'tags', function($join) {
            $join->on('articles.id', '=', 'tags.article_id');
        });
    }
}
