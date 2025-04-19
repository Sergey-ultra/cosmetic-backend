<?php

namespace App\Repositories\ArticleRepository;


use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\User;
use App\Models\UserInfo;
use App\Services\EntityStatus;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ArticleRepositoryRepository implements IArticleRepository
{
    /**
     * @return Builder
     */
    public function getAdminArticleList(): Builder
    {
        $viewsCountSubQuery = DB::table('article_views')
            ->select([DB::raw('count(ip_address) as view_count'), 'article_id'])
            ->groupBy('article_id');

        return DB::table('articles')
            ->select(
                'articles.id as id',
                'articles.title as title',
                'articles.preview as preview',
                'article_categories.name AS category_name',
                'articles.status as status',
                'articles.created_at as created_at',
                'users.name as user',
                'article_views.view_count'
            )
            ->leftJoinSub($viewsCountSubQuery, 'article_views', function ($join) {
                $join->on('articles.id', '=', 'article_views.article_id');
            })
            ->leftJoin('article_categories', 'article_categories.id', '=', 'articles.article_category_id')
            ->join('users', 'articles.user_id', '=', 'users.id');
    }

    public function getSingleArticleBySlug(string $slug): Model|EloquentBuilder|null
    {
        $viewsCountSubQuery = DB::table('article_views')
            ->select([DB::raw('count(ip_address) as count'), 'article_id'])
            ->groupBy('article_id');

        return Article::query()->select(
            'articles.id',
            'articles.title',
            'articles.slug',
            'articles.preview',
            'articles.body',
            'articles.image',
            'articles.created_at',
            'users.name AS user_name',
            'article_categories.id AS category_id',
            'article_categories.name AS category_name',
            'article_categories.color AS category_color',
            'user_infos.avatar AS user_avatar',
            DB::raw('IF(article_views.count IS NOT NULL, article_views.count, 0) AS views_count')
        )
            ->with([
                'tags',
                'comments' => function ($query) {
                    $query
                        ->select([
                            sprintf('%s.id', ArticleComment::TABLE),
                            sprintf('%s.name AS user_name', User::TABLE),
                            sprintf('%s.avatar as user_avatar', UserInfo::TABLE),
                            'reply_id',
                            'article_id',
                            'comment',
                            DB::raw(sprintf('DATE(%s.created_at) AS created_at', ArticleComment::TABLE))
                        ])
                        ->with('likes')
                        ->leftJoin(
                            User::TABLE,
                            sprintf('%s.user_id', ArticleComment::TABLE),
                            '=',
                            sprintf('%s.id', User::TABLE)
                        )
                        ->leftjoin(
                            UserInfo::TABLE,
                            sprintf('%s.user_id', ArticleComment::TABLE),
                            '=',
                            sprintf('%s.user_id', UserInfo::TABLE)
                        )
                        ->where(sprintf('%s.status', ArticleComment::TABLE), EntityStatus::PUBLISHED)
                        ->orderBy(sprintf('%s.created_at', ArticleComment::TABLE), 'DESC');
                }])
            ->leftJoinSub($viewsCountSubQuery, 'article_views', function ($join) {
                $join->on('articles.id', '=', 'article_views.article_id');
            })
            ->join('users', 'articles.user_id', '=', 'users.id')
            ->leftJoin('user_infos', 'users.id', '=', 'user_infos.user_id')
            ->leftJoin('article_categories', 'article_categories.id', '=', 'articles.article_category_id')
            ->where(['articles.slug' => $slug, 'articles.status' => EntityStatus::PUBLISHED])
            ->first();
    }
}
