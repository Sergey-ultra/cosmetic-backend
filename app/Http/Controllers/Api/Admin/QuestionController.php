<?php

declare(strict_types=1);


namespace App\Http\Controllers\Api\Admin;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProvider;
use App\Http\Requests\StatusRequest;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    use DataProvider;

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) ($request->per_page ?? 10);

        $query = Question::query();
        $result = $this->prepareModel($request, $query)->paginate($perPage);

        return response()->json(['data' => $result]);
    }

    public function setStatus(int $id, StatusRequest $request): JsonResponse
    {
        Question::where('id', $id)->update(['status' => $request->status]);
        return response()->json(['data' => ['status' => 'success']]);
    }
}
