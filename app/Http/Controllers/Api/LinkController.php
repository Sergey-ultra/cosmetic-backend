<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Link;
use App\Models\LinkClick;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;


class LinkController extends Controller
{
    public function linkByCode(string $code): JsonResponse|RedirectResponse
    {
        $link = Link::query()->where('code', $code)->first();

        if (!$link) {
            return response()->json(['message' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            DB::beginTransaction();
            $link->clicks++;
            $link->save();

            $visitorIp = request()->ip();

            LinkClick::query()->create([
                'ip' => $visitorIp,
                'link_id' => $link->id
            ]);
            DB::commit();

            return redirect($this->addQueryParams($link->link));
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Ошибка  LinkController ' . $e->getMessage()], 500);
        }
    }

    protected function addQueryParams(string $url): string
    {
        $urlParts = parse_url($url);

        if (isset($urlParts['query'])) {
            parse_str($urlParts['query'], $params);
        } else {
            $params = [];
        }


        $params['utm_source'] = 'smart-beautiful.ru';
        $params['utm_medium'] = 'site';
        $params['utm_campaign'] = 'main';
        $params['utm_content'] = 'product_card';


        $urlParts['query'] = http_build_query($params);

        return  $urlParts['scheme'] . '://' . $urlParts['host'] . $urlParts['path'] . '?' . $urlParts['query'];
    }


}
