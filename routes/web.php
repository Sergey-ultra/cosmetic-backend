<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LinkController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('config:cache');
    return 'DONE'; //Return anything
});




//Route::get('/article/{id}', [ArticleController::class, 'article']);


Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'emailVerify'])
    ->name('verification.verify');

//Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'emailVerify'])
//->middleware(['auth', 'signed'])->name('verification.verify');



Route::get('/to/{code}', [LinkController::class, 'linkByCode']);

Route::get('{any}', [MainController::class, 'main'])
    ->where('any', '^(?!admin|email\/verify|to|supplier).*$')
    ->name('app');

Route::get('/admin{any}', function() {
    return view('admin');
})->where('any', '.*');

Route::get('/supplier{any}', function() {
    return view('supplier');
})->where('any', '.*');




//Route::get('{admin?}', function(){
//    return view('admin');
//})->where('admin', '.*$');

//Route::get('/', function(){
//    return view('layouts.master');
//})->name('home');





