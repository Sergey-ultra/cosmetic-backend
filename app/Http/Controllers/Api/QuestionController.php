<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProvider;
use App\Http\Requests\QuestionRequest;
use App\Http\Resources\MyQuestionCollection;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    use DataProvider;

    public function my(Request $request): ResourceCollection
    {
        $perPage = (int) ($request->per_page ?? 10);

        $query = Question::select([
            'products.name AS sku_name',
            'products.code AS product_code',
            'skus.id AS sku_id',
            'skus.volume',
            'skus.images AS sku_images',
            'questions.body',
            'questions.status',
            'questions.created_at',
            'users.name AS user_name',
            'user_infos.avatar'
        ])
            ->join('skus', 'skus.id', '=', 'questions.sku_id')
            ->join('products', 'skus.product_id', '=', 'products.id')
            ->join('users', 'users.id', '=', 'questions.user_id')
            ->leftjoin('user_infos', 'users.id', '=', 'user_infos.user_id')
            ->where([
                'questions.user_id' => Auth::id(),
                ['questions.status', '!=', 'published']
            ])
        ;

        $result = $this->prepareModel($request, $query)->paginate($perPage);

        return new MyQuestionCollection($result);
    }

    /**
     * @params \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function bySkuId(Request $request): JsonResponse
    {
        $perPage = (int) ($request->per_page ?? 10);

        $query = Question::where([
            'sku_id' => $request->sku_id,
            'status' => 'published'
            ])
        ->orderBy('created_at', 'DESC');

        $result = $this->prepareModel($request, $query)->paginate($perPage);

        return response()->json(['data' => $result]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\QuestionRequest  $request
     * @return JsonResponse
     */
    public function store(QuestionRequest $request): JsonResponse
    {
        $skuId = $request->sku_id;
        $user = Auth::guard('sanctum')->user();
        $visitorIp = request()->ip();

        $question = Question::create(
            [
                'ip_address' => $visitorIp,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'sku_id' => $skuId,
                'body' => $request->body,
            ]
        );
        return response()->json([
            'data' => [
                'status' => 'success',
                'data' => $question
            ]
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * @param  int  $id
     */
    public function destroy($id)
    {
        Question::where('id', $id)->update(['status' => 'deleted']);
    }
}
