<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
       $bannerUrls = [
           '/storage/banner/banner1.jpg',
           '/storage/banner/banner2.jpg',
       ];
       return response()->json(['data' => $bannerUrls]);
    }
}
