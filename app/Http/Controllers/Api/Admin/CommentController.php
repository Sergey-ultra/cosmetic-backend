<?php

declare(strict_types=1);


namespace App\Http\Controllers\Api\Admin;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProvider;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
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
        $perPage = (int)  ($request->per_page ?? 10);

        $query = Comment::query()
            ->select([
                sprintf('%s.id', Comment::TABLE),
                sprintf('%s.review_id', Comment::TABLE),
                sprintf('%s.comment', Comment::TABLE),
                sprintf('%s.reply_id', Comment::TABLE),
                sprintf('%s.status', Comment::TABLE),
                sprintf('%s.created_at', Comment::TABLE),
                sprintf('%s.name AS user_name', User::TABLE),
            ])
            ->leftJoin(
                User::TABLE,
                sprintf('%s.user_id', Comment::TABLE),
                '=',
                sprintf('%s.id', User::TABLE)
            );
        $result = $this->prepareModel($request, $query)->paginate($perPage);

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
