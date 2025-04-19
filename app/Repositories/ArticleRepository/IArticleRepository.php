<?php

namespace App\Repositories\ArticleRepository;

use Illuminate\Database\Query\Builder;

interface IArticleRepository
{
    public function getAdminArticleList(): Builder;
}
