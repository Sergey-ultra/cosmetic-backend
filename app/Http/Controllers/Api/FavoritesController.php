<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Sku;
use App\Models\SkuUser;
use App\Services\PriceHistoryService\PriceHistoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoritesController extends Controller
{
    public function index(): JsonResponse
    {
        $ids = SkuUser::query()
            ->select('sku_id')
            ->where('user_id', Auth::id())
            ->get()
            ->pluck('sku_id')
            ->all()
        ;

        return response()->json([ 'data' => $ids ]);
    }

    public function showFavoriteSkus(PriceHistoryInterface $priceService): JsonResponse
    {
        $skus = Sku::query()
            ->select(
                'skus.id',
                'skus.rating',
                'skus.reviews_count',
                'skus.volume',
                'skus.images',
                'products.name',
                'products.code'
            )
            ->with('priceDynamics')
            ->join('sku_user', 'sku_user.sku_id', '=', 'skus.id')
            ->join('products', 'products.id', '=', 'skus.product_id')
            ->where('sku_user.user_id', Auth::id())
            ->paginate(12);


        foreach ($skus as &$sku) {
            $sku['image'] = $sku->image;


            if (count($sku->priceDynamics)) {
                $prices = $sku->priceDynamics->sortBy('created_at')->toArray();
                unset($sku->priceDynamics);
                $sku['priceDynamics'] = $priceService->makePriceDynamics($prices);
            } else {
                $sku['priceDynamics'] = [];
            }
        }


        return response()->json(['data' => $skus]);
    }


    public function store(Request $request)
    {
        $id = $request->id;

        SkuUser::query()->create([
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
