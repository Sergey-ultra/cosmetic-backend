<?php

namespace App\Http\Controllers;


use App\Http\Resources\ArticleCollection;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{


    public function article($id)
    {
        $links = [
            [ 'title' => 'Бренды', 'url' =>   'brands'],
            [ 'title' => 'Сыворотки', 'url' => 'category/serums'],
            ['title' => 'Увлажняющие кремы', 'url' => 'category/cream'],
            ['title' => 'Масла', 'url' => 'category/oil'],
            ['title' => 'Тоники', 'url' => 'category/tonic'],
            ['title' =>  'Пилинг', 'url'  => 'category/pilling'],
            ['title' => 'Скраб', 'url' => 'category/scrab'],
            ['title' => 'Средства для умывания', 'url' => 'category/umjivanie'],
            ['title' => 'Маски', 'url' =>  'category/maski'],
        ];

        $article = Article::find($id);
        return view('article', compact('links', 'article'));
    }
}
