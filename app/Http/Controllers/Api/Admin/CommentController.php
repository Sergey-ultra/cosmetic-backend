<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProviderWithDTO;
use App\Http\Controllers\Traits\ParamsDTO;
use App\Models\Comment;
use App\Models\Product;
use App\Models\Review;
use App\Models\Sku;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    use DataProviderWithDTO;

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int)($request->per_page ?? 10);

        $query = Comment::query()
            ->select([
                sprintf('%s.id', Comment::TABLE),
                sprintf('%s.comment', Comment::TABLE),
                sprintf('%s.reply_id', Comment::TABLE),
                sprintf('%s.status', Comment::TABLE),
                sprintf('%s.created_at', Comment::TABLE),
                sprintf('%s.review_id', Comment::TABLE),
                DB::raw(sprintf("CONCAT(%s.code,'-',%s.id) AS product_link", Product::TABLE, Sku::TABLE)),
                sprintf('%s.volume', Sku::TABLE),
                sprintf('%s.name AS user_name', User::TABLE),
            ])
            ->join(
                Review::TABLE,
                sprintf('%s.review_id', Comment::TABLE),
                '=',
                sprintf('%s.id', Review::TABLE)
            )
            ->join(
                Sku::TABLE,
                sprintf('%s.sku_id', Review::TABLE),
                '=',
                sprintf('%s.id', Sku::TABLE)
            )
            ->join(
                Product::TABLE,
                sprintf('%s.product_id', Sku::TABLE),
                '=',
                sprintf('%s.id', Product::TABLE)
            )
            ->leftJoin(
                User::TABLE,
                sprintf('%s.user_id', Comment::TABLE),
                '=',
                sprintf('%s.id', User::TABLE)
            );

        $paramsDto = new ParamsDTO(
            $request->input('filter', []),
            $request->input('sort', ''),
        );
        $result = $this->prepareModel($paramsDto, $query)->paginate($perPage);

        return response()->json(['data' => $result]);
    }


    /**
     * @param int $id
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setStatus(int $id, Request $request): JsonResponse
    {
        $comment = Comment::find($id);
        if (!$comment) {
            return response()->json(['data' => ['status' => false]], 404);
        }

        $comment->update(['status' => $request->status]);

        return response()->json(['data' => ['status' => true]]);
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
