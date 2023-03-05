<?php

namespace App\Http\Controllers;

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
}
