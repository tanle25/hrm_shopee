<?php

namespace App\Http\Controllers;


use App\Facade\ScraperFacade;
use App\Models\Image;
use App\Models\Option;
use App\Models\Product;
use App\Models\Variant;
use App\Models\video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

class HomeController extends Controller
{
    //


    function getProduct(Request $request)
    {
        ScraperFacade::setCookie($request->cookie);
        $this->detectURL($request->url);
    }

    function getByAPI()
    {

        # code...


        $url = 'https://shopee.vn/VOUCHER-SHOPEE-VIDEO-GI%E1%BA%A2M-60k-TUY%E1%BA%BET-MAI-SI%C3%8AU-N%E1%BB%A4-C%C3%80NH-TO-HOA-TUY%E1%BA%BET-MAI-R%E1%BB%AANG-NG%E1%BB%A6-%C4%90%C3%94NG-KHO%E1%BA%A2NG-1m2-7-NG%C3%80Y-N%E1%BB%9E-i.44030757.6375334471?publish_id=&sp_atk=eb2346d1-228d-4a05-9e8f-f48386a1c2ef&xptdk=eb2346d1-228d-4a05-9e8f-f48386a1c2ef';
        $this->detectURL($url);
        dd('done');
    }

    function detectURL($url)
    {

        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $parse = parse_url($url);
            // dd($parse);
            switch ($parse['host']) {
                case 'shopee.vn':
                    # code...
                    $pattern = '/\d+\.\d+/';
                    if (preg_match($pattern, $url, $matches)) {
                        $extractedValue = $matches[0]; // The first match in the results
                        // dd($extractedValue);
                        $value = explode('.', $extractedValue);
                        $shopID = intval($value[0]);
                        $productID = intval($value[1]);
                        $t = ScraperFacade::scraper($shopID, $productID, $url);
                        dd($t);
                        $this->createProduct($t);
                    } else {
                        // echo "No match found.\n";
                        dd('No match found.');
                    }
                    break;

                default:
                    # code...
                    dd('not support');
                    break;
            }
        } else {
            dd('url invalid');
        }
    }

    function createProduct($t)
    {
        // dd($t);
        // str_replace()
        $product = Product::create([
            'product_id' => $t->item_id,
            'name' => $t->tracking->name,
            'slug' => Str::slug($t->tracking->name),
            'image' => 'https://down-vn.img.susercontent.com/file/' . $t->image->image,
            'price' => $t->tracking->price,
            'min_price' => $t->info->price_min,
            'max_price' => $t->info->price_max,
            'price_before_discount' => $t->info->price_before_discount,
            'price_min_before_discount' => $t->info->price_min_before_discount,
            'price_max_before_discount' => $t->info->price_max_before_discount,
            'stock' => $t->info->stock,
            'like' => $t->info->liked_count,
            'discount' => $t->info->discount_lable,
            'content' => $t->content
        ]);
        foreach ($t->variations as $v) {

            $variant = Variant::create([
                'product_id' => $product->id,
                'name' => $v->title
            ]);
            foreach ($v->options as $op) {

                $option = Option::create([
                    'variant_id' => $variant->id,
                    'name' => $op->name,
                    'image' => 'https://down-vn.img.susercontent.com/file/' . $op->image,
                ]);
            }
        }
        foreach ($t->image->images as $image) {
            $image = Image::create([
                'product_id' => $product->id,
                'image' => 'https://down-vn.img.susercontent.com/file/' . $image
            ]);
        }

        if (!empty($t->image->video)) {
            $video = $t->image->video[0];
            video::create([
                'product_id' => $product->id,
                'url' => $video->formats[0]->url
            ]);
        }
    }

    function getFormat() {
        // $video_url = "https://www.facebook.com/watch?v=1044277350025090";
        $video_url = "https://www.youtube.com/watch?v=MW7oIHvPpdc&t=51s";
        // $video_url = "https://www.tiktok.com/@mr.3pro3/video/7304508537827233057";
        //ID                    EXT RESOLUTION |   FILESIZE   TBR PROTO | VCODEC ACODEC MORE INFO
        // ID  EXT   RESOLUTION FPS CH |   FILESIZE  TBR PROTO | VCODEC         VBR ACODEC      ABR ASR MORE INFO
        $fomat = array(['ID','EXT', 'RESOLUTION', 'FILESIZE']);
        exec("yt-dlp -F --print ext $video_url 2>&1 ", $text);
        $formats = array_slice($text,2,count($text) - 3);
        dd($formats);
        foreach($formats as $format){
            $a = explode('|', $format);
            $id_t = preg_replace('/\s+/', ' ', $a[0]);
            $fomat['ID'][] = explode(' ', $id_t)[0];
            $ext = explode(' ', $id_t)[1];
            $fomat['EXT'][] = $ext;
        }
        dd($fomat);
    }


}
