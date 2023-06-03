<?php

namespace App\Services\ArticleService;

use Illuminate\Database\Query\Builder;

interface IArticle
{
    public function getAdminArticleList(): Builder;
}
