<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Sku;
use App\Models\SkuUser;
use App\Services\PriceHistoryService\PriceHistoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoritesController extends Controller
{
    public function index()
    {
        $ids = SkuUser::select('sku_id')->where('user_id', Auth::id())->get()->pluck('sku_id')->all();

        return response()->json([ 'data' => $ids ]);
    }

    public function showFavoriteSkus(PriceHistoryService $priceService)
    {
        $ids = SkuUser::select('sku_id')->where('user_id', Auth::id())->get();

        $skus = Sku::with(['product', 'priceDynamics'])->whereIn('id', $ids)->paginate(12);

        foreach ($skus as &$sku) {
            $sku['name'] = $sku->product->name;
            $sku['code'] = $sku->product->code;
            $sku['images'] = json_decode( $sku['images'], true)[0];

            $prices = $sku->priceDynamics->sortBy('created_at')->toArray();
            unset($sku->priceDynamics);


            $sku['priceDynamics'] = $priceService->makePriceDynamics($prices);

            unset($sku['product']);
            unset($sku['created_at']);
            unset($sku['updated_at']);
            unset($sku['deleted_at']);
            unset($sku['product_id']);
        }


        return response()->json([ 'data' => $skus]);
    }


    public function store(Request $request)
    {
        $id = $request->id;

        SkuUser::create([
            'user_id' => Auth::id(),
            'sku_id' => $id
        ]);
        return response()->json([
            'data' => [
                'status' => true,
                'message' => 'success'
            ]
        ]);
    }


    public function destroy($id)
    {
        SkuUser::where([
            'user_id' => Auth::id(),
            'sku_id' => $id
        ])->delete();

        return response()->json([
            'data' => [
                'status' => true,
                'message' => 'success'
            ]
        ]);
    }
}
