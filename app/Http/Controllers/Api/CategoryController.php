<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;



class CategoryController extends Controller
{
    public function nested()
    {
        $result = Category::select(['id', 'name', 'code', 'image'])->whereNotNull('parent_id')->get();
        return response()->json(['data' =>  $result ]);
    }


    public function byIds($ids)
    {
        $result = Category::select('id', 'name')->whereIn('id', $ids)->get();
        return response()->json(['data' => $result]);
    }
}
