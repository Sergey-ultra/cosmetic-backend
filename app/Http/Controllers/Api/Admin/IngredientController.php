<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProvider;
use App\Models\ActiveIngredientsGroup;
use App\Models\Ingredient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IngredientController extends Controller
{
    use DataProvider;

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {

        $perPage = (int) ($request->per_page ?? 10);

        if ($perPage === -1) {
            $result = Ingredient::select(['id', 'name', 'name_rus'])->orderBy('name')->get();
            return response()->json( $result );
        }


        $query = DB::table('ingredients')
            ->select([
                "ingredients.id as id",
                "ingredients.name as name",
                "ingredients.name_rus as name_rus",
                "ingredients.code as code",
                "ingredients.description as description",
                "active_ingredients_groups.name as active_ingredients_group_name"
            ])
            ->leftJoin('active_ingredients_group_ingredient', 'ingredients.id','=', 'active_ingredients_group_ingredient.ingredient_id')
            ->leftJoin('active_ingredients_groups', 'active_ingredients_group_ingredient.active_ingredients_group_id', '=', 'active_ingredients_groups.id')
        ;

        $result = $this->prepareModel($request, $query,true)->paginate($perPage);
        return response()->json( $result );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showAvailableActiveIngredientsGroups(): JsonResponse
    {
        $result = ActiveIngredientsGroup::select('id', 'name')->get();
        return response()->json(['data' => $result]);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $ingredient = Ingredient::select('id', 'name','name_rus', 'description')->where('id', $id)->first();
        return response()->json(['data' => $ingredient]);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $code): JsonResponse
    {
        $ingredient = Ingredient::where('code', $code)->first();
        if ($ingredient) {
            $params = $request->all();

            if ($request->has('image')) {
                $params['image'] = json_encode($request->image);
            }

            $ingredient->update($params);
            return response()->json([
                'status' => true,
                'data' => $ingredient
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id): void
    {
        Ingredient::destroy($id);
    }
}
