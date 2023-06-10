<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProvider;
use App\Models\Comment;
use App\Services\TreeService\TreeInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    use DataProvider;

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function byReviewId(Request $request, TreeInterface $treeService): JsonResponse
    {
        //$result = Comment::getNestedComments($request->review_id);
        $comments = DB::table('comments')
            ->select(
            'comments.id',
            'comments.user_name',
            'comments.reply_id',
            'comments.review_id',
            'comments.comment',
            DB::raw('DATE(comments.created_at) AS created_at'),
            'user_infos.avatar as user_avatar'

        )
            ->leftjoin('user_infos', 'comments.user_id', '=', 'user_infos.user_id')
            ->where([
                'comments.review_id' => $request->review_id,
                'comments.status' => 'published',
            ])
            ->orderBy('created_at', 'DESC')
            ->get()
            ->map(fn ($row) => get_object_vars($row))
            ->toArray();
        ;

        $result = $treeService->buildTree($comments, 'reply_id');

        return response()->json(['data' => $result]);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function my(Request $request): JsonResponse
    {
        $perPage = (int)($request->per_page ?? 10);
        $query = Comment::select([
            'reviews.comment as body',
            'reviews.plus',
            'reviews.minus',
            'comments.comment',
            'comments.id',
            'comments.status',
        ])
            ->join('reviews', 'comments.review_id', '=', 'reviews.id')
            //->join('sku_ratings', 'reviews.sku_rating_id', '=', 'sku_ratings.id')
            //->join('skus', 'sku_ratings.sku_id', '=', 'skus.id')
            //->join('products', 'skus.product_id', '=', 'products.id')
            ->where('comments.user_id', Auth::id())
            ->where('comments.status', '<>', 'deleted')
        ;
        $result = $this->prepareModel($request, $query, true)->paginate($perPage);

        return response()->json([ 'data'=> $result ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $params = $request->all();
        $user = Auth::user();
        $params['user_id'] = $user->id;


        $newComment = Comment::query()->create($params);

        return response()->json([
            'data' => [
                'status' => true,
                'data' => $newComment
            ]
        ], 201);
    }




    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy(int $id): void
    {
        Comment::where('id', $id)->update(['status' => 'deleted']);
    }
}
