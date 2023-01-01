<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\LinkClick;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LinkClickController extends Controller
{
   public function index(): JsonResponse
   {
       $result = LinkClick::select(DB::raw("count(id) as count,  DATE(created_at) AS date"))
           ->groupBy(DB::raw('WEEK(created_at)'))
           ->get();

       return response()->json(['data' =>  $result]);
   }

    public function clicksByStore(): JsonResponse
    {
        $result = DB::table('links')
            ->selectRaw("sum(links.clicks) as sum,  stores.name as name")
            ->join('stores', 'links.store_id', '=', 'stores.id')
            ->groupBy(DB::raw('stores.name'))
            ->orderBy('sum', 'DESC')
            ->get();

        return response()->json(['data' =>  $result]);
    }
}
