<?php

namespace App\Services\ArticleService;


use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ArticleService implements IArticle
{
    /**
     * @return \Illuminate\Database\Query\Builder
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
}
