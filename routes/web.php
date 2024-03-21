<?php

use App\Models\Product;
use Weidner\Goutte\GoutteFacade;
use App\Facade\LazadaScraperFacade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\HomeController;
use App\Facade\ScraperFacade;
use App\Http\Controllers\ShopeeController;
use Symfony\Component\DomCrawler\Crawler;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {

return view('shopeeurl');
});

Route::get('video',[HomeController::class,'getFormat']);

Route::get('admin',function(){
    return view('admin');
});
Route::get('shopee-url',function(){
    return view('shopeeurl');
});
Route::get('shopee',[ShopeeController::class,'index']);
Route::get('product',[ShopeeController::class,'getProducts']);

Route::post('get-product',[HomeController::class,'getProduct']);
Route::get('export',[ShopeeController::class,'exportExcel']);


Route::get('scrape',[HomeController::class,'home']);
Route::get('scrape1',[HomeController::class,'getByAPI']);
Route::get('scrape2',[HomeController::class,'getProductinfo']);
// Route::post('get-product-shopee',[ShopeeController::class,'getP']);
Route::post('get-product-shopee',[ShopeeController::class,'getByURL']);


Route::get('xoa-san-pham/{id}',[ShopeeController::class,'deleteProduct']);




Route::get('test',[HomeController::class,'test']);

