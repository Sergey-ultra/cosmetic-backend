<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sku;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FilterController extends Controller
{
   public function index(Request $request): JsonResponse
   {
       $categoryCode = $request->category_code;
       $brandCode = $request->brand_code;

       $search = $request->search;


       $brandIds =  $request->brand_ids;
       $categoryIds =  $request->category_ids;
       $volumes =  $request->volumes;
       $maxPrice =  $request->max_price;
       $minPrice =  $request->min_price;



       $productSubQuery = Product::query()->select(['id', 'category_id', 'brand_id']);

       if ($categoryCode) {
           $category = Category::query()->where('code', $categoryCode)->first();
           if (!$category) {
               return response()->json(['error' => 'Not Found'], 404);
           }


           $productSubQuery->where('category_id','=', $category->id);
       } else if ($brandCode) {
           $brand = Brand::query()->where('code', $brandCode)->first();
           if (!$brand) {
               return response()->json(['error' => 'Not Found'], 404);
           }

           $productSubQuery->where('brand_id','=', $brand->id);
           //$productSubQuery = DB::raw("(SELECT id, category_id, brand_id FROM products WHERE brand_id=$brandId) AS `products`");

       }  else if($search) {
           $productSubQuery->where('name', 'LIKE', "%$search%");
           //$productSubQuery = DB::raw("(SELECT id, brand_id, category_id FROM products WHERE name LIKE '%$search%') AS `products`");
       }



       $select = [
           'skus.volume as volume',
           'products.id as id',
           'products.brand_id as brand_id'
       ];

       if (!$categoryCode) {
           $select[] = 'products.category_id as category_id';
       }

       $skusGroupByQuery = DB::table('sku_store')->distinct()->select('sku_id');

       $skuProductIdsSubQuery = Sku::select(
           'skus.product_id as product_id',
           'skus.volume as volume'
       )
           ->joinSub($skusGroupByQuery, 'sku_store', function ($join) {
               $join->on( 'skus.id', '=', 'sku_store.sku_id');
           });



       $productsWithSkusWithoutPrices = DB::table($productSubQuery, 'products')
           ->select($select)
           ->joinSub($skuProductIdsSubQuery, 'skus', function ($join) {
               $join->on("skus.product_id", "=", "products.id");
           });



       $skuWithPricesSubQuery = Sku::select(
           'sku_store.price as price',
           'skus.product_id as product_id',
           'skus.volume as volume'
       )
           ->join('sku_store', 'skus.id', '=', 'sku_store.sku_id');

       $select[] = 'skus.price as price';

       $productsWithPriceSubQuery = DB::table($productSubQuery, 'products')
           ->select($select)
           ->joinSub($skuWithPricesSubQuery, 'skus', function ($join) {
               $join->on("skus.product_id", "=", "products.id");
           });





//       if ($brandIds) {
//           $productsWithPriceSubQuery = $productsWithPriceSubQuery->whereIn('brand_id', $brandIds);
//       }
//       if ($categoryIds) {
//           $productsWithPriceSubQuery = $productsWithPriceSubQuery->whereIn('category_id', $categoryIds);
//       }
//       if ($volumes) {
//           $productsWithPriceSubQuery = $productsWithPriceSubQuery->whereIn('volume', $volumes);
//       }
//       if ($minPrice) {
//           $productsWithPriceSubQuery = $productsWithPriceSubQuery->where('price', '>', $minPrice);
//       }
//       if ($maxPrice) {
//           $productsWithPriceSubQuery = $productsWithPriceSubQuery->where('price', '<', $maxPrice);
//       }






       $minMaxPrices = DB::table($productsWithPriceSubQuery)
           ->select(DB::raw('MAX(price) as max, MIN(price) as min'))
           ->get();




       $volumes = DB::table($productsWithSkusWithoutPrices)
           ->select(DB::raw('volume as name, count(volume) as count'))
           ->groupBy('volume')
           ->orderBy('count', 'desc')
           ->get();

       $data = [];
       $data['volumes'] = $volumes;



//
//       $ingredients = DB::table($productsWithSkusWithoutPrices, 'products')
//           ->selectRaw(
//               'active_ingredients_groups.id AS id,
//           active_ingredients_groups.name as name,
//           COUNT(active_ingredients_groups.name) AS count'
//           )
//           ->join('ingredient_product','products.id', '=', 'ingredient_product.product_id')
//           ->join('active_ingredients_group_ingredient', 'ingredient_product.ingredient_id', '=', 'active_ingredients_group_ingredient.ingredient_id')
//           ->join('active_ingredients_groups', 'active_ingredients_group_ingredient.active_ingredients_group_id', '=','active_ingredients_groups.id')
//           ->groupBy('name')
//           ->orderBy('name')
//           ->get();


       $activeIngredientsQuery = DB::table('active_ingredients_groups')
           ->selectRaw(
               'active_ingredients_groups.id AS id,
               active_ingredients_groups.name AS name,
               ingredient_product.product_id AS product_id'
           )
           ->join('active_ingredients_group_ingredient', 'active_ingredients_group_ingredient.active_ingredients_group_id', '=','active_ingredients_groups.id')
           ->join('ingredient_product', 'ingredient_product.ingredient_id', '=', 'active_ingredients_group_ingredient.ingredient_id')
           ->groupBy('product_id', 'id', 'name')
       ;

       $ingredients = DB::table($productsWithSkusWithoutPrices, 'products')
           ->selectRaw(
               'ag.id AS id, ag.name AS name, COUNT(ag.name) AS count'
           )
           ->joinSub($activeIngredientsQuery,'ag', function($join){
               $join->on('products.id', '=', 'ag.product_id');
           })
           ->groupBy( 'ag.name', 'ag.id' )
           ->orderBy('ag.name')
           ->get();

       $data['ingredients'] = $ingredients;





       $extractsQuery = DB::table('extracts')
           ->selectRaw(
               'extracts.id AS id,
               extracts.name AS name,
               ingredient_product.product_id AS product_id'
           )
           ->join('extract_ingredient', 'extract_ingredient.extract_id', '=','extracts.id')
           ->join('ingredient_product', 'ingredient_product.ingredient_id', '=', 'extract_ingredient.ingredient_id')
           ->groupBy('product_id', 'id', 'name')
       ;

       $extracts = DB::table($productsWithSkusWithoutPrices, 'products')
           ->selectRaw(
               'ex.id AS id, ex.name AS name, COUNT(ex.name) AS count'
           )
           ->joinSub($extractsQuery,'ex', function($join){
               $join->on('products.id', '=', 'ex.product_id');
           })
           ->groupBy( 'name', 'id' )
           ->orderBy('name')
           ->get();

       $data['extracts'] = $extracts;




       if (!isset($brandCode)) {

           $brands = DB::table($productsWithSkusWithoutPrices, 'products')
               ->select('brands.name as name', 'brands.id as id', DB::raw('count(products.id) as  count'))
               ->join("brands","products.brand_id", "=", "brands.id")
               ->groupBy('name', 'id')
               ->orderBy('name', 'asc')
               ->get();





           $data['brands'] = $brands;

       }

       if (!isset($categoryCode)) {

           $categories = DB::table('categories')
               ->select('categories.name as name', 'categories.id as id', DB::raw('count(categories.id) as  count'))
               ->joinSub($productsWithSkusWithoutPrices, 'products', function ($join) {
                   $join->on("products.category_id", "=", "categories.id");
               })
               ->groupBy('name', 'id')
               ->orderBy('name', 'asc')
               ->get();

           $data['categories'] = $categories;
       }



       $countries = DB::table($productsWithSkusWithoutPrices, 'products')
           ->select('countries.id as id', 'countries.name as name', DB::raw('count(products.id) as  count'))
           ->join("brands","products.brand_id", "=", "brands.id")
           ->join("countries","brands.country_id", "=", "countries.id")
           ->groupBy('name', 'id')
           ->orderBy('name', 'asc')
           ->get();
       $data['countries'] = $countries;


       $purposes= Product::distinct()
           ->select(DB::raw('purpose as name, count(purpose) as count'))
           ->whereNotNull('purpose')
           ->groupBy('purpose')
           ->orderBy('name')
           ->get();








       $data['purposes'] = $purposes;
       $data['min_price'] = $minMaxPrices[0]->min;
       $data['max_price'] = $minMaxPrices[0]->max;

       return response()->json(['data' => $data]);
   }

   public function receipts(Request $request)
   {
       $categoryCode = $request->category_code;

       if ($categoryCode && in_array($categoryCode, ['serums', 'cream'])) {
           return response()->json(['data' =>
               [
                   ['name' => 'С ресвератролом'],
                   ['name' => 'с пептидами'],
                   ['name' => 'с церамидами'],
                   ['name' => 'с ретинолом'],
                   ['name' => 'с витамином C']
               ]
           ]);
       }

       return response()->json(['data' => []]);
   }
}
