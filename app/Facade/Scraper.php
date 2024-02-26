<?php
namespace App\Facade;

use stdClass;
use Weidner\Goutte\GoutteFacade;
use Symfony\Component\DomCrawler\Crawler;

class Scraper{

    public $discount_lable;
    public $info;
    public $cookie = '';
    function scraper($shopID, $productID, $url){

        $product = (object) $this->getProductInfo($shopID, $productID);
        $product->image = (object) $this->getImage($shopID, $productID);
        $product->info = $this->info;
        $product->content = $this->getContent($url);
        return $product;
    }

    function setCookie($cookie){
        $this->cookie = $cookie;
    }

    function getProductInfo($shopID, $productID) {
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
        $headers[] = 'Cookie: '.$this->cookie;
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

        // dd(json_decode($result));

        $item = json_decode($result)->data->item;


        $info['item_id'] = $item->item_id;
        $info['item_price'] = $item->item_price;
        $info['tracking'] = $item->tracking;
        $info['variations'] = $item->tier_variations;

        $info['models'] = $this->mergeVariant($item);

        // $options = $item->tier_variations[0]->options;
        // $models = $item->models;
        // // dd($models, $options);
        // foreach($models as $key=> $o){

        //     $options[$o->tier_index[0]]->model = $o;

        // }

        // $info['variations']['options'] = $options;
        // dd($models, $options);
        // dd($info);
        // return $info;
        // dd($info);
        return $info;

        // return $status;

    }

    function getContent($url) {
        $html = file_get_contents($url);
        $crawler = new Crawler($html);
        $content = $crawler->filter('p')->reduce(function (Crawler $node) {
            return $node;
        });

        return $content->html();

        // return implode("\n",$content);
    }

    function getImage($shopID, $productID) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://shopee.vn/api/v4/recommend/recommend?bundle=shop_page_product_tab_main&limit=99999&offset=0&section=shop_page_product_tab_main_sec&shopid='.$shopID);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

        $headers = array();
        $headers[] = 'Authority: shopee.vn';
        $headers[] = 'Accept: application/json';
        $headers[] = 'Accept-Language: en-US,en;q=0.9,vi;q=0.8';
        $headers[] = 'Af-Ac-Enc-Dat: AAczLjQuMS0yAAABjR+1K/sAABB+AyAAAAAAAAAAAv+Jk48S5p7olfvwn34XQN5E76dIl/82EyfDx/bVRcPaaRvYm5f/NhMnw8f21UXD2mkb2JtDUhoWBlPwi8TQ+9bf9liWKr3qoQBN9yFfoL6AGs/AM3zCb9pyXV3CRNllgQiacRAEj558wiFfd+9s1ukK44giFZ4BcwmlQkQC+nCQWug2jN6STyB8Ba0umHlN8QP3jdRNQI8oXGxhoSmvigaM+BOkdwsqacvl3DsqlRdQmwdpHhPUMoMZfaLYsbSO3qPCMUAPUBRtXc1VdYE/XObLjkTeD/E/0kOWe+Ks1/RbWdfu7U/THtiA58j3b/+xXHEl7LlRAl+rcrUAbUSko7rXqS2LBtjs/0ocwvppC0XgWUp3rAhnu6B//6SUqMLtxMfuxHzBtfb6hsFnQW3si1z+nDRansmpFtCLio4F9fmX30otNF60y893sHbg7RtslXVSZJbBtfb6hsFnQW3si1z+nDRansmpFtCLio4F9fmX30otNBrHx+IaMLQ4H9LYNsVH9E/Cxc01hPIrcdLMIahX/gFdRyR+f9qpnYRQEwLc++Ab9RjPpF2Dxr+VDN9qkxGXAPFgfqglJtScW8UF234O1l4nRyR+f9qpnYRQEwLc++Ab9dLnqMXJ4WyHVrx9kjVZsGjQCFbKogj/4WaVWmSZKvVcjlQjUDLDHsYyhi0uQy08IkS85LLr8voufDwECzYNbOA0FtV8H5Ssusl5rhDm3VwNDXw8F7nvkwowyE23ow0C5olsrMQgZMoYpyb07JLAQGf+1nkZU/fw+k3TN+WR0Mr+L2Bav7CtgS8Mo5BC6hysEr1EUDD5JkcnpbdTXJWJA98Lmhcn2lZQMacWokZpQDmrDvlGWYtHPfg9VBOFlU5Shpf/NhMnw8f21UXD2mkb2JtaGu6mk1vlSSndtvlmO5TN4uhx7GFU04ZgyUCN1moQ4JBJM185VJop2ase7oss/H+78xErTydZF+3iHR5gC55udwqEtQ+y14d2AmfUhSE1FIVa+TrVaDdwUqQmVH2KrCRUB0yC9KUFDtLkDqfAETW7sgsevlXzM3UwhMsW07bSMQ==';
        $headers[] = 'Af-Ac-Enc-Sz-Token: Q7OxSSXiobKBRug29cZqcA==|VCJvngfm4BW/L3PwA8Q5Yf7VyEIV08olYswwTDfmhFK8hiphF5hqBqIhrMr4EdQXJVLUYXTBgQEoWUI+|vJ1eKZ6kKe//SZMW|08|3';
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Cookie: '.$this->cookie;
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
        // dd($result);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        $items = json_decode($result)->data->sections[0]->data->item;
        // dd($items[0]);
        $info = new stdClass();
        $image =  new stdClass();
        // dd(json_decode($result)->data->sections[0]->data->item);
        foreach($items as $i){
            if($i->itemid == $productID){
                $image->image = $i->image;
                $image->images = $i->images;
                $image->video = $i->video_info_list;
                $info->stock = $i->stock;
                $info->liked_count = $i->liked_count;
                $info->discount_lable = $i->raw_discount;
                $info->price = $i->price;
                $info->price_min = $i->price_min;
                $info->price_max = $i->price_max;
                $info->price_min_before_discount = $i->price_min_before_discount;
                $info->price_max_before_discount = $i->price_max_before_discount;
                $info->price_before_discount = $i->price_before_discount;
            }
        }

        $this->info = $info;
        return $image;

    }

    function mergeVariant($item) {
        $models = $item->models;
        $variants = $item->tier_variations;
        // dd($models, $variants, $value);
        foreach($models as $model){
            $value = array();
            foreach($model->tier_index as $key => $index){
                $value[] = $variants[$key]->options[$index];
            }

            $model->variant = $value;
        }
        return $models;
    }
}
