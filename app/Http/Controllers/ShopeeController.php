<?php

namespace App\Http\Controllers;

use stdClass;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\ProductExport;
use Illuminate\Support\MessageBag;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\DomCrawler\Crawler;

class ShopeeController extends Controller
{
    //
    function index()
    {
        return view('shopee');
    }



    function getByURL(Request $request, MessageBag $message_bag)
    {

        $request->validate([
            'url' => 'url|required'
        ]);

        $pattern = '/\d+\.\d+/';


        $url = $this->reconstruct_url($request->url);
        if (!preg_match($pattern, $url, $matches)) {
            return back()->withErrors('url', 'Không phải URL sản phẩm');
        }

        $headers = [
            'Access-Control-Allow-Origin:' => 'https://muathongminh.vn',
            'sec-fetch-user' => '?1',
            'sec-ch-ua-mobile' => '?0',
            'sec-fetch-site' => 'none',
            'sec-fetch-dest' => 'document',
            'sec-fetch-mode' => 'navigate',
            'cache-control' => 'max-age=0',
            // 'authority' => 'www.facebook.com',
            'upgrade-insecure-requests' => '1',
            'accept-language' => 'en-GB,en;q=0.9,tr-TR;q=0.8,tr;q=0.7,en-US;q=0.6',
            'sec-ch-ua' => '"Google Chrome";v="89", "Chromium";v="89", ";Not A Brand";v="99"',
            'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',
            'accept' => 'application/json, text/plain, */*'
        ];




        $context = stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'header' => implode("\r\n", array_map(function ($v, $k) {
                    return "$k: $v";
                }, $headers, array_keys($headers)))
            ]
        ]);

        $data = $this->getByApi($url);
        // dd($data);
        if ($data === null || $data->status !== 'success' || $data->data->product_base === null) {

            return back();
        }

            $data = $data->data->product_base;
            $product = new stdClass();
            $product->id = $data->product_id_platform;
            $product->shopid = (int)$data->shop_id_platform;
            $product->shopname = null;
            $product->shopurl = "https://shopee.vn/shop/".$data->shop_id_platform;
            $product->title = $data->name;
            $product->image = $data->url_thumbnail;
            $product->images = $data->url_images;
            $product->price = (int) $data->price;
            $product->price_min = (int) $data->price_insight->min_price;
            $product->price_max = (int) $data->price_insight->max_price;
            $product->price_min_bf = null;
            $product->price_max_bf = null;
            $product->description = $data->description;
            $product->content = $data->description;
            $product->stock = null;
            $product->liked_count = $data->like_count;

            $product->rating = $data->rating_count;
            $product->sold = $data->sold;
            $product->video = "";

            $product->rating_star = $data->rating_avg;
            $product->url = $request->url;
            $product->lazada = $request->lazada ? $this->reconstruct_url($request->lazada) : null;
            $product->tiki = $request->tiki ?  $this->reconstruct_url($request->tiki) : null;
            $product->category = $data->categories[count($data->categories)-1]->name;
            $product->attributes = $data->attributes;
            $product->brand = $data->brand;
            $product->lazPrice = null;
            $product->tikiPrice = null;

            if ($request->tiki) {
                $product->tikiPrice = $this->getTikiPrice($request->tiki);
            }
            if ($request->lazada) {
                $product->lazPrice  = $this->getPriceLazada($request->lazada);
            }

            // dd($product);
            $session = Session::push('products', $product);
            return back();

    }

    function getProducts()
    {
        // Session::forget('products');
        $products = Session::get('products');

        dd($products);
    }

    function reconstruct_url($url)
    {
        $url_parts = parse_url($url);
        $constructed_url = $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'];

        return $constructed_url;
    }
    function exportExcel()
    {
        $products = Session::get('products');
        try {
            //code...
            // Excel::store(new ProductExport($products),'products.xlsx');
            Session::forget('products');
            // return response()->download(storage_path('app/products.xlsx'))->deleteFileAfterSend(true);
            return Excel::download(new ProductExport($products), 'products.xlsx');
        } catch (\Throwable $th) {
            //throw $th;
            dd($th);
            return back()->withErrors(['err' => 'Lỗi']);
        }
    }
    function deleteProduct($id)
    {

        $products = Session::get('products');

        foreach ($products as $key => $product) {
            # code...
            if ($product->id == $id) {
                unset($products[$key]);
                break;
            }
        }
        Session::put('products', $products);

        return back();
    }

    function getTikiPrice($url)
    {
        $html = file_get_contents($url);

        $crawler = new Crawler($html);
        $data = $crawler->filterXpath('//*[@id="__next"]/div[1]/main/div/div[2]/div[1]/div[1]/div[1]/div[2]/div/div[1]/div/div/div[2]/div/div/div[1]');
        $text = $data->text();
        $price = preg_replace('/[^0-9]/', '', $text);
        return intval($price);
    }

    function getPriceLazada($url)
    {
        $html = file_get_contents($url);
        $crawler = new Crawler($html);
        $script = $crawler->filter('script:contains("__moduleData__")');
        $text = Str::between($script->text(), '__moduleData__ =', '; var __googleBot__ ');
        $data = json_decode($text)->data->root->fields;
        // dd($data);
        $price = preg_replace('/[^0-9]/', '', html_entity_decode($data->tracking->pdt_price));
        // dd($price, $data);
        return intval($price);
    }

    function formatPrice($price): int
    {

        $value = $price > 0 ? $price / 100000 : $price;
        return $value;
    }

    function getByApi($url)
    {
        $shopData = $this->findShopeeId($url);
        if ($shopData != null) {

            $headers = [
                'sec-fetch-user' => '?1',
                'sec-ch-ua-mobile' => '?0',
                'sec-fetch-site' => 'none',
                'sec-fetch-dest' => 'document',
                'sec-fetch-mode' => 'navigate',
                'cache-control' => 'max-age=0',
                // 'authority' => 'www.facebook.com',
                'upgrade-insecure-requests' => '1',
                'accept-language' => 'en-GB,en;q=0.9,tr-TR;q=0.8,tr;q=0.7,en-US;q=0.6',
                'sec-ch-ua' => '"Google Chrome";v="89", "Chromium";v="89", ";Not A Brand";v="99"',
                'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',
                'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9'
            ];

            $context = stream_context_create([
                'http' => [
                    'ignore_errors' => true,
                    'header' => implode("\r\n", array_map(function ($v, $k) {
                        return "$k: $v";
                    }, $headers, array_keys($headers)))
                ]
            ]);
            $productId = $shopData['shop_id'];
            $shopId = $shopData['product_id'];

            $api = "https://apiv3.beecost.vn/product/detail?product_base_id=1__" . $shopId . "__" . $productId . "&type=new";
            // dd($api);
            $data = file_get_contents($api, false, $context);
            return json_decode($data);
        } else {
            return back();
        }
    }

    function findShopeeId($url)
    {
        $url = preg_replace('/\?.*/', '', $url);
        $pattern = '/(?:\.(\d+)\.(\d+)$)|(?:\/(\d+)\/(\d+))/';

        if (preg_match($pattern, $url, $matches)) {
            $shopId = $matches[1] ?? $matches[3];
            $productId = $matches[2] ?? $matches[4];
            if ($productId && $shopId) {
                return [
                    'shop_id' => $shopId,
                    'product_id' => $productId
                ];
            }
        }

        return null;
    }

    function getShopInfo($shopID, $productID) {
        $cookies = 'SPC_F=jkWlJWHc7i3PFapsHq6rhfwwGEN70x06; REC_T_ID=9a066df8-b5e6-11ee-bbb6-fe5cd1c91611; _gcl_au=1.1.2046695257.1705571253; _fbp=fb.1.1705571253133.1267733951; SPC_CLIENTID=amtXbEpXSGM3aTNQzffeghhfvhgrkepi; _hjSessionUser_868286=eyJpZCI6Ijk0YmJjMjUwLTNmY2EtNTI0MC04MjJiLTg3YzY4NTA5ZGU3MyIsImNyZWF0ZWQiOjE3MDU1NzEyNTUxMjQsImV4aXN0aW5nIjp0cnVlfQ==; SC_DFP=DunJECmjBzZJzFSPCIahdDRFqYgygzyb; _ga_4GPP1ZXG63=deleted; _gcl_aw=GCL.1709624213.CjwKCAiA_5WvBhBAEiwAZtCU79foE42cpl7ntJSCQXMWnbsJiT7MsxJUNakUiQnUEHD3eGn8-EzKzhoCgOcQAvD_BwE; _gac_UA-61914164-6=1.1709624214.CjwKCAiA_5WvBhBAEiwAZtCU79foE42cpl7ntJSCQXMWnbsJiT7MsxJUNakUiQnUEHD3eGn8-EzKzhoCgOcQAvD_BwE; SPC_EC=.b1VpWHM1V1FQZlVlME1PN5dEIrKag92ta/VFbUj8ovAoFMT6ByP/TBl4XsEIYgiuMAMgDhw2j4abjVaQRnOruQXoAd6azUBtFEEslircGkLnGPGrjuZyr5jQJAIrNhpXllqmBJ0Sm53okfva+auWikAqgJSDg8Wznh4NGHDOafpH4CTV8g6IWx8G+RytAEGLMEam08tX6h1PvbdofTO5DQ==; SPC_ST=.b1VpWHM1V1FQZlVlME1PN5dEIrKag92ta/VFbUj8ovAoFMT6ByP/TBl4XsEIYgiuMAMgDhw2j4abjVaQRnOruQXoAd6azUBtFEEslircGkLnGPGrjuZyr5jQJAIrNhpXllqmBJ0Sm53okfva+auWikAqgJSDg8Wznh4NGHDOafpH4CTV8g6IWx8G+RytAEGLMEam08tX6h1PvbdofTO5DQ==; SPC_SC_TK=a8aeddb77584a4265f395bfca6bc98e2; SPC_SC_UD=20634467; SPC_SC_SESSION=f6483985b4fe1b21344962b183126ac5_1_20634467; SPC_STK=edGXcIboxt74veIOnm6kN1X+LxK5ZTBwq8Fls7Nhq0biVMpYLtkdyA7frkbNLlsMR2GFZ9sZLnW/iEEb4AygVGUOiHhAU9wfH4Pql8Xpl+xIMUhmKWG7WHcLAeTS1cPKnE5kw1TdJdLArjjBV+MIKUaXsxmE/7ZUC6ZCmsEBHlnlhrHRjkWGqe1yxv4bXtYC/IHHp8pyBqgZFDnDVY0lYg==; SPC_U=20634467; SPC_R_T_ID=jy4s7aznIBmPiNvCcVztWzH9cy40m8oEi5b45wWLgX6K0CoXlkV0D8CpF0Cl8GZZaLaOfiD5jgdDVGy4X309LT7PikpjwcFdIiJFznydHQPaqX4IOt0nr87BbpSsCXZbBbFZDRxAaUkjDT9WdNw/UM7tv+oeb3jYAZ7V3O1ACj8=; SPC_R_T_IV=RGtzRnFRMnlrMVNxd2h6VA==; SPC_T_ID=jy4s7aznIBmPiNvCcVztWzH9cy40m8oEi5b45wWLgX6K0CoXlkV0D8CpF0Cl8GZZaLaOfiD5jgdDVGy4X309LT7PikpjwcFdIiJFznydHQPaqX4IOt0nr87BbpSsCXZbBbFZDRxAaUkjDT9WdNw/UM7tv+oeb3jYAZ7V3O1ACj8=; SPC_T_IV=RGtzRnFRMnlrMVNxd2h6VA==; CTOKEN=XzcssuNuEe6s%2F7LTH4tHkA%3D%3D; SPC_SI=zRfwZQAAAABYYXlFSmRhNXLRnwAAAAAAUDk5SUVxaTI=; SPC_SEC_SI=v1-ZkNoY1F4dHFxTWUxeENGVRZbxweMsj6xa4GfGUFxql3kdwIb0Xm5P46hvEuSaWSbyUR4xYFizfs5+wzSDQbiuvQe/vF9breTtkh+NIFJG8o=; _gid=GA1.2.2078690869.1710747099; __LOCALE__null=VN; csrftoken=RRYn9MbNUvd82sY7CyIxJjmw0UtqBRAN; _sapid=a318e7f985e96b9cc26149829fb1302dc7deac680c76dbbf88982bf3; _QPWSDCXHZQA=61f4cdd5-03b3-4217-fad7-8fcd1d564863; REC7iLP4Q=03623ad3-3d41-4533-b66c-bba458c6ef07; SPC_IA=1; _med=affiliates; _hjSession_868286=eyJpZCI6ImEzYThhMjhjLWU5YWUtNGU5OS04M2RkLTU4ZGNlNDU3YjhmMSIsImMiOjE3MTA4Mjk1OTk0OTQsInMiOjAsInIiOjAsInNiIjowLCJzciI6MCwic2UiOjAsImZzIjowLCJzcCI6MX0=; AC_CERT_D=U2FsdGVkX18UvkgCJNXRs//EqTTuEt5as5T3lXekkNU6NxXSmX+K4QWYfl8L50a+kV7coxF17wEqrHJKPkXcaF5yOTcvsCEb9+122BSZuSuWj8AZd289c7m9NeLOKM8XsquOJfSv/bYanw2GsqQ4dkv8FdrWo7sPqN7B1HReznLIYC/B+avw7vkIPVJ++9eDUQhSttN5Zm01/XKKUtfBx0fozAEcjLEbfpCdQcd8Y6E6dTgtToDgwnVEsb76mwLpp6q0J2ydTiNRbaG7imKEso8CjnWr+ovTqKzuYS/bHnsmuV/0oSR7ZUNw82AQxO/ccUbZAzkEiAAtn9qcjzOVNkYlzr9ivxfgujPPJ5BnDyTbZYdnTjp2Bfm/pswX4W/B; AMP_TOKEN=%24NOT_FOUND; shopee_webUnique_ccd=FmS6Lpvn10ZA5%2BriiECsIA%3D%3D%7CtoFEXenYP%2Bn%2FcBuY9Typ4%2Fnot0XBBXQq2Cyzs%2BwjRrO948%2B6p8JskFim48ZvrYGhpRo6BI6AD0PnAE%2BC%7CiKWSkWozhivu65cc%7C08%7C3; ds=bde4a2aa72ec97d782a2c9390cf10d69; _ga_4GPP1ZXG63=GS1.1.1710829601.111.1.1710831407.55.0.0; _ga=GA1.1.2033148046.1705571254; _ga_3XVGTY3603=GS1.1.1710831756.1.1.1710831785.31.0.0';
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://shopee.vn/api/v4/pdp/cart_panel/get');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"shop_id\":$shopID,\"item_id\":$productID,\"quantity\":1}");
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

        $headers = array();
        $headers[] = 'Authority: shopee.vn';
        $headers[] = 'Accept: application/json';
        $headers[] = 'Accept-Language: en-US,en;q=0.9,vi;q=0.8';
        $headers[] = 'Af-Ac-Enc-Dat: AAczLjQuMS0yAAABjR+1K/sAABB+AyAAAAAAAAAAAv+Jk48S5p7olfvwn34XQN5E76dIl/82EyfDx/bVRcPaaRvYm5f/NhMnw8f21UXD2mkb2JtDUhoWBlPwi8TQ+9bf9liWKr3qoQBN9yFfoL6AGs/AM3zCb9pyXV3CRNllgQiacRAEj558wiFfd+9s1ukK44giFZ4BcwmlQkQC+nCQWug2jN6STyB8Ba0umHlN8QP3jdRNQI8oXGxhoSmvigaM+BOkdwsqacvl3DsqlRdQmwdpHhPUMoMZfaLYsbSO3qPCMUAPUBRtXc1VdYE/XObLjkTeD/E/0kOWe+Ks1/RbWdfu7U/THtiA58j3b/+xXHEl7LlRAl+rcrUAbUSko7rXqS2LBtjs/0ocwvppC0XgWUp3rAhnu6B//6SUqMLtxMfuxHzBtfb6hsFnQW3si1z+nDRansmpFtCLio4F9fmX30otNF60y893sHbg7RtslXVSZJbBtfb6hsFnQW3si1z+nDRansmpFtCLio4F9fmX30otNBrHx+IaMLQ4H9LYNsVH9E/Cxc01hPIrcdLMIahX/gFdRyR+f9qpnYRQEwLc++Ab9RjPpF2Dxr+VDN9qkxGXAPFgfqglJtScW8UF234O1l4nRyR+f9qpnYRQEwLc++Ab9dLnqMXJ4WyHVrx9kjVZsGjQCFbKogj/4WaVWmSZKvVcjlQjUDLDHsYyhi0uQy08IkS85LLr8voufDwECzYNbOA0FtV8H5Ssusl5rhDm3VwNDXw8F7nvkwowyE23ow0C5olsrMQgZMoYpyb07JLAQGf+1nkZU/fw+k3TN+WR0Mr+L2Bav7CtgS8Mo5BC6hysEr1EUDD5JkcnpbdTXJWJA98Lmhcn2lZQMacWokZpQDmrDvlGWYtHPfg9VBOFlU5Shpf/NhMnw8f21UXD2mkb2JtaGu6mk1vlSSndtvlmO5TN4uhx7GFU04ZgyUCN1moQ4JBJM185VJop2ase7oss/H+78xErTydZF+3iHR5gC55udwqEtQ+y14d2AmfUhSE1FIVa+TrVaDdwUqQmVH2KrCRUB0yC9KUFDtLkDqfAETW7sgsevlXzM3UwhMsW07bSMQ==';
        $headers[] = 'Af-Ac-Enc-Sz-Token: Q7OxSSXiobKBRug29cZqcA==|VCJvngfm4BW/L3PwA8Q5Yf7VyEIV08olYswwTDfmhFK8hiphF5hqBqIhrMr4EdQXJVLUYXTBgQEoWUI+|vJ1eKZ6kKe//SZMW|08|3';
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Cookie: '.$cookies;
        $headers[] = 'Sec-Ch-Ua: \"Not_A Brand\";v=\"8\", \"Chromium\";v=\"120\", \"Google Chrome\";v=\"120\"';
        $headers[] = 'Sec-Ch-Ua-Mobile: ?0';
        $headers[] = 'Sec-Ch-Ua-Platform: \"macOS\"';
        $headers[] = 'Sec-Fetch-Dest: empty';
        $headers[] = 'Sec-Fetch-Mode: cors';
        $headers[] = 'Sec-Fetch-Site: same-origin';
        $headers[] = 'Sz-Token: Q7OxSSXiobKBRug29cZqcA==|VCJvngfm4BW/L3PwA8Q5Yf7VyEIV08olYswwTDfmhFK8hiphF5hqBqIhrMr4EdQXJVLUYXTBgQEoWUI+|vJ1eKZ6kKe//SZMW|08|3';
        $headers[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
        $headers[] = 'X-Api-Source: pc';
        $headers[] = 'X-Csrftoken: 6SoCfXKdNRuXT21npeZyh0HmUaHLffv2';
        $headers[] = 'X-Requested-With: XMLHttpRequest';
        $headers[] = 'X-Sap-Ri: d7e8a9656805e3afbaf6483903015abd6ed21badf8fcd8e35517';
        $headers[] = 'X-Sap-Sec: PR1fYXt9MStW+StWN1tn+SGWN1tW+SGW+StX+StWjS8W+oG9+StD+StWyP76UahW+StI+1tWcS8W+JcyBXi0Mio9n17n9v71n5AK9HkobU8S1UbOsJG8B6LpZyug7oPv3A8AJVZeH9sn1eC1mUkyEA/wrd+2kPgQ5Xj899fROHWnL3mVTaQe+w/6CeT45Kn3BBih18w2juNc8cXE5PvN5k3H9xVtPLrHsHLBsWi2ORnY3H/LiodAlKd9sFKJdmHO/Ub3WOImv9idQY9db1SrsDQg1mFLYv7DH8p2xWctiz6gqxksLMalIdy0dIrMjWM1z4JTpfN7d4tH5/IX4HkYHeFUcC8LFtMtjy14g9jeowg9UIX7DQzmrnEt+TlKciLN3U0zQAWSRPBYCR8wEgKTvHZvSV3reRWYkvV0CblBzr3OJmWF6B8ccJhATnsYf8LRkrB8RNS2KESSY1QFyhI/BzfdSLjOIktclravGYn0HEacdCdUpqt68O1kI5aqoxbAphZptwhVVX4+bdG8ZHG2XTej83LghlOV/5EzvpJWx3VEYXDILywCuC7YYcWfQRteFdGA9cV0okJiqS5l/zWTVGOkKByz30FpThILijoOOHLCoJNUCxYwyuXfwdljWE+LsKHU53NoqYjmT8PyZF6wWvVoEt4VIRb9UNB9tiL6I4h9PN7DBMqQO3wAO5JeDEjlNLl3AEjtSZLvC68eJQheF1LnAFCuHTsDPjMgNJgEagLIG3NpFgcHT6OVcFuBAxDpGX4zYtWdVbj2/r9kEDuZRjSymVdbL1hW+Stc/3gB/3mKoatW+SqvUgd6BStW+HLW+Stl+StWT6LIeNbf1eMRqTq73BkJMK1nwi8n+StW6eW4oZU+6CUW+StWBStv+ShWNStn+StWBStW+HLW+Stl+StWHdELgeuewmy/HKxPdmR8F3+uIemn+StW7ngM7Us4p3eW+StW';
        $headers[] = 'X-Shopee-Language: vi';
        $headers[] = 'X-Sz-Sdk-Version: 3.4.1-2&1.6.14';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        $status = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);
        return json_decode($result) ;
    }
}
