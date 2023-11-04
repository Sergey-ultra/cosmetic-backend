<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProviderWithDTO;
use App\Http\Controllers\Traits\ParamsDTO;
use App\Http\Requests\StatusRequest;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    use DataProviderWithDTO;

    public function index(Request $request): JsonResponse
    {
        $perPage = (int)($request->per_page ?? 10);

        $paramsDto = new ParamsDTO(
            $request->input('filter', []),
            $request->input('sort', ''),
        );

        $result = $this->prepareModel($paramsDto, Question::query())->paginate($perPage);

        return response()->json(['data' => $result]);
    }

    public function setStatus(int $id, StatusRequest $request): JsonResponse
    {
        Question::where('id', $id)->update(['status' => $request->status]);
        return response()->json(['data' => ['status' => 'success']]);
    }
}
