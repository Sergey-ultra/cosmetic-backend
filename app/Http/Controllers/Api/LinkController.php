<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\AdminNotificationJob;
use App\Models\Link;
use App\Models\LinkClick;
use Illuminate\Support\Facades\DB;


class LinkController extends Controller
{
    public function linkByCode($code)
    {
        $link = Link::where('code', $code)->first();

        if ($link) {
            try {
                DB::beginTransaction();
                $link->clicks++;
                $link->save();

                $visitorIp = request()->ip();

                LinkClick::create([
                    'ip' => $visitorIp,
                    'link_id' => $link->id
                ]);
                DB::commit();

                if ($visitorIp !== config('telegrambot.admin_ip')) {
                    $message = "Кто-то c ip $visitorIp  перешел по ссылке  $link->link";
                    AdminNotificationJob::dispatch($message);
                }


                $link =  $this->addQueryParams($link->link);

                return redirect($link);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['message' => 'Ошибка  LinkController'. $e->getMessage()], 500);
            }
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
