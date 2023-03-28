<?php

namespace App\Http\Controllers;

use App\Models\Sku;
use App\Models\Visit;
use Illuminate\Http\Request;

class MainController extends Controller
{

    public function main(Request $request)
    {
        //счетчик заходов на spa фронт
        $name = (new Visit())->getConnectionName();
//        dd(config('database'));
        Visit::hit($request);


        //return redirect('http://localhost:3000');
        return view('app');
    }

    public function test()
    {
        $repare = Sku::query()->where('id','>', 3099)->get();
        foreach($repare as $sku) {
            $sku->images = json_decode($sku->images);

            $sku->update();
        }
        echo 'success';
    }
}
