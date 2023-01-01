<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class VisitStaticticsController extends Controller
{
   public function index(): JsonResponse
   {
       $result = DB::table('visit_statistic')
           ->select(DB::raw("count(ip) as count,  DATE(created_at) AS date"))
           ->groupBy(DB::raw('DATE(created_at)'))
           //->orderBy(DB::raw('WEEK(created_at)'))
           ->get();

       return response()->json(['data' =>  $result]);
   }
}
