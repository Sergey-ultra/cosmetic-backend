<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PriceHistory;
use App\Services\PriceHistoryService\PriceHistoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PriceHistoryController extends Controller
{

    public function index(Request $request, PriceHistoryService $priceService)
    {
        $skuId = $request->sku_id;
        $sqlMode =  $request->sql_mode ?? true;


        if ($sqlMode) {
            $result = DB::table('price_histories')
                ->select(DB::raw("AVG(price) AS avg, MAX(price) AS max, MIN(price) AS min,  DATE(created_at) AS date"))
                ->where('sku_id', $skuId)
                ->whereNotNull('price')
                ->groupBy(DB::raw('WEEK(created_at)'))
                ->orderBy(DB::raw('date'))
                ->get();

            return response()->json(['data' =>  $result]);
        }




        $prices = PriceHistory::select('price', 'created_at')
            ->where('sku_id', $skuId)
            ->whereNotNull('price')
            ->orderBy('created_at')
            ->get()
            ->toArray();

        $result = $priceService->makePriceDynamics($prices);

        return response()->json(['data' =>  $result]);
    }
}
