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
    function index() {
        return view('shopee');
    }

    // function getP(Request $request) {

    //     $html = $request->content;

    //     $element = new stdClass();

    //     $crawler = new Crawler($html);

    //     // dd($crawler);

    //     $content = $crawler->filter('.e8lZp3')->reduce(function (Crawler $node) {
    //         return $node;
    //     });


    //     $price = $crawler->filter('.G27FPf')->reduce(function (Crawler $node) {
    //         return $node;
    //     });
    //     $title = $crawler->filter('.WBVL_7 span')->reduce(function (Crawler $node) {
    //         return $node;
    //     });

    //     $shopname = $crawler->filter('.fV3TIn')->reduce(function (Crawler $node) {
    //         return $node;
    //     });

    //     $category = $crawler->filter('.idLK2l a')->last();

    //     $sex = $crawler->filterXPath('//*[@id="main"]/div/div[2]/div[1]/div/div/div/div[3]/div/div[1]/div[1]/section[1]/div/div[2]/div');

    //     $stok = $crawler->filterXPath('//*[@id="main"]/div/div[2]/div[1]/div/div/div/div[3]/div/div[1]/div[1]/section[1]/div/div[3]/div');
    //     $shop_url = $crawler->filterXPath('//*[@id="main"]/div/div[2]/div[1]/div/div/div/section[2]/div[1]/div/div[3]/a')->attr('href');
    //     $shop_url = "https://shopee.vn$shop_url";
    //     $element->content = $content->html();
    //     $element->price = $price->text();
    //     $element->title = $title->text();
    //     $element->shopname = $shopname->text();
    //     $element->url = $shop_url;
    //     $element->category = $category->text();
    //     $element->sex = $sex->text();
    //     $element->stock = $stok->text();
    //     $images = $crawler->filterXPath('/html/head/script[20]')->reduce(function (Crawler $node) {
    //         return $node;
    //     });
    //     // $data = $crawler->filterXPath('//*[@id="main"]/div/div[2]/div[1]/div/div/div/div[3]/div/div[1]/div[1]/section[1]/div/div[2]/div');
    //     $data = '{"@context":"http://schema.org","@type":"Product","name":"Găng Tay Da Thời Trang Nam Cao Cấp Cảm Ứng Điện Thoại, Chống Nước, Chống Trơn Trượt","description":"Găng tay da thời trang nam cao cấp cảm ứng điện thoại sẽ là lựa chọn tuyệt vời để bạn giữ ấm mùa đông.Chất liệu da cao cấp: Găng tay được làm từ chất liệu da cao cấp, giúp bảo vệ đôi tay của bạn khỏi lạnh và thời tiết khắc nghiệt.Cảm ứng điện thoại: Thiết kế đặc biệt cho phép bạn sử dụng điện thoại mà không phải tháo găng ra ngoài.Chống nước và chống mài mòn: Găng tay được thiết kế để chống lại các yếu tố bên ngoài như nước, bụi và các vật sắc nhọn, giúp kéo dài tuổi thọ sản phẩm của bạn.Lót nỉ giữ ấm: Lớp lót bên trong được làm từ chất liệu nhẹ và êm ái có khả năng giữ ấm cho đôi tay của bạn trong suốt ngày dài.Giang Tay Da Thoi Trang Nam Cao Cấp Cam ứng điện Thoai - sản phẩm không chỉ mang lại tính tiện ích khi sử dụng hàng ngày, mà còn mang tính thẩm mỹ khiến cho người sử dụng trở lên sang trọng hơn. Hơn hết, sản phẩm này hoàn toàn phù hợp với nam giới.\nChế độ bảo hành:Tất cả sản phẩm găng tay của chúng tôi được bảo hành trong vòng 7 Ngày kể từ ngày mua.với các trường hợp như:Hàng không đúng chủng loại, mẫu mã như quý khách đặt hàngKhông đủ số lượng, không đủ bộ như trong đơn hàngTình trạng bên ngoài bị ảnh hưởng như rách bao bì, bể vỡ… Từ chối bảo hành:Quá thời hạn bảo hành ( 7 Ngày kể từ khi nhận hàng)Hàng gửi lại không đúng mẫu mã ( không phải sản phẩm của Simba E-Shop)Đặt nhầm sản phẩm, chủng loại, không thích, không hợp,...","url":"https://shopee.vn/Găng-Tay-Da-Thời-Trang-Nam-Cao-Cấp-Cảm-Ứng-Điện-Thoại-Chống-Nước-Chống-Trơn-Trượt-i.958693576.23761901196","productID":"23761901196","image":"https://down-vn.img.susercontent.com/file/sg-11134201-7rbm7-lop3ollhrgzr06","brand":"","offers":{"@type":"AggregateOffer","lowPrice":"59000.00","highPrice":"99000.00","priceCurrency":"VND","seller":{"@context":"http://schema.org","@type":"Organization","name":"Đồ Chơi  Simba ","url":"https://shopee.vn/khosisimba","image":"https://down-vn.img.susercontent.com/file/3f3e84dbae56b2ee797fa2cc219299e9","aggregateRating":{"@type":"AggregateRating","bestRating":5,"worstRating":1,"ratingCount":"3395","ratingValue":"4.91"}},"itemCondition":"NewCondition","availability":"http://schema.org/InStock"}}';
    //     $data1 = '{"@context":"http://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"item":{"@id":"https://shopee.vn/","name":"Shopee"}},{"@type":"ListItem","position":2,"item":{"@id":"https://shopee.vn/Thời-Trang-Nam-cat.11035567","name":"Thời Trang Nam"}},{"@type":"ListItem","position":3,"item":{"@id":"https://shopee.vn/Phụ-Kiện-Nam-cat.11035567.11035627","name":"Phụ Kiện Nam"}},{"@type":"ListItem","position":4,"item":{"@id":"https://shopee.vn/Găng-tay-cat.11035567.11035627.11035637","name":"Găng tay"}},{"@type":"ListItem","position":5,"item":{"@id":"https://shopee.vn/Găng-Tay-Da-Thời-Trang-Nam-Cao-Cấp-Cảm-Ứng-Điện-Thoại-Chống-Nước-Chống-Trơn-Trượt-i.958693576.23761901196","name":"Găng Tay Da Thời Trang Nam Cao Cấp Cảm Ứng Điện Thoại, Chống Nước, Chống Trơn Trượt"}}]}';

    //     dd(json_decode($data), json_decode($data1));
    // }

    function getByURL(Request $request, MessageBag $message_bag) {

        $request->validate([
            'url'=>'url|required'
        ]);

        $pattern = '/\d+\.\d+/';


        $url = $this->reconstruct_url($request->url);
        if (!preg_match($pattern, $url, $matches)) {
            return back()->withErrors('url','Không phải URL sản phẩm');
        }

        $html = file_get_contents($request->url);

        $crawler = new Crawler($html);

        $data = $crawler->filterXpath('//script[@type="text/mfe-initial-data"]');

        $data = json_decode( $data->text())->initialState->DOMAIN_PDP->data;

        $data = reset($data->PDP_BFF_DATA->cachedMap);
        // dd($data);

        $product = new stdClass();
        $product->id = $data->item->item_id;
        $product->shopid = $data->item->shop_id;
        $product->shopname = $data->shop_detailed->name;
        $product->shopurl = "https://shopee.vn/". $data->shop_detailed->account->username;
        $product->title = $data->item->title;
        $product->image = $data->item->image;
        $product->images = $data->item->images;
        $product->price = $this->formatPrice($data->item->price);
        $product->price_min = $this->formatPrice($data->item->price_min);
        $product->price_max = $this->formatPrice($data->item->price_max);
        $product->price_min_bf = $this->formatPrice($data->item->price_min_before_discount);
        $product->price_max_bf = $this->formatPrice($data->item->price_max_before_discount);
        $product->description = $data->item->description;
        $product->content = empty($data->item->rich_text_description) ? null : $data->item->rich_text_description->paragraph_list;
        $product->stock = $data->item->stock;
        $product->liked_count = $data->item->liked_count;

        $product->rating = $data->product_review->total_rating_count;
        $product->sold = $data->item->global_sold;
        $product->video = empty($data->item->video_info_list) ? null : $data->item->video_info_list[0]->formats[count($data->item->video_info_list[0]->formats)-1]->url;

        $product->rating_star = $data->item->item_rating->rating_star;
        $product->url = $request->url;
        $product->lazada = $request->lazada ? $this->reconstruct_url($request->lazada) : null;
        $product->tiki = $request->tiki ?  $this->reconstruct_url($request->tiki) :null;
        $product->id = $data->item->item_id;
        $product->category = $data->item->fe_categories[count($data->item->fe_categories)-1]->display_name;
        $product->attributes = $data->product_attributes->attrs;
        $product->brand = $data->item->brand;
        $product->lazPrice = null;
        $product->tikiPrice = null;

        if($request->tiki){
            $product->tikiPrice = $this->getTikiPrice($request->tiki);
        }
        if($request->lazada){
            $product->lazPrice  = $this->getPriceLazada($request->lazada);
        }

        $session = Session::push('products',$product);
        return back();
    }

    function getProducts() {
        // Session::forget('products');
        $products = Session::get('products');

        dd($products);

    }

    function reconstruct_url($url){
        $url_parts = parse_url($url);
        $constructed_url = $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'];

        return $constructed_url;
    }
    function exportExcel() {
        $products = Session::get('products');
        try {
            //code...
            Excel::store(new ProductExport($products),'products.xlsx');
            Session::forget('products');
            return response()->download(storage_path('app/products.xlsx'))->deleteFileAfterSend(true);

        } catch (\Throwable $th) {
            //throw $th;
            dd($th);
            return back()->withErrors('err','Lỗi');
        }


    }
    function deleteProduct($id) {

        $products = Session::get('products');

        foreach ($products as $key => $product) {
            # code...
            if($product->id == $id){
                unset($products[$key]);
                break;
            }
        }
        Session::put('products',$products);

        return back();


    }

    function getTikiPrice($url) {
        $html = file_get_contents($url);

        $crawler = new Crawler($html);
        $data = $crawler->filterXpath('//*[@id="__next"]/div[1]/main/div/div[2]/div[1]/div[1]/div[1]/div[2]/div/div[1]/div/div/div[2]/div/div/div[1]');
        $text = $data->text();
        $price = preg_replace('/[^0-9]/', '', $text);
        return intval($price);
    }

    function getPriceLazada($url) {
        $html = file_get_contents($url);
        $crawler = new Crawler($html);
        $script = $crawler->filter('script:contains("__moduleData__")');
        $text = Str::between($script->text(),'__moduleData__ =','; var __googleBot__ ');
        $data = json_decode($text)->data->root->fields;
        // dd($data);
        $price = preg_replace('/[^0-9]/', '', html_entity_decode($data->tracking->pdt_price));
        // dd($price, $data);
        return intval($price);
    }

    function formatPrice($price) : int {

        $value = $price > 0 ? $price /100000 : $price;
        return $value;
    }
}
