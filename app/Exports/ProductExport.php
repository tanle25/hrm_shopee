<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;

class ProductExport implements FromArray, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $products;

    public function __construct( $products)
    {
        $this->products = $products;
    }
    public function array(): array
    {
        //
        return $this->products;

    }
    public function headings(): array
    {
        return [
        "ID",
        "ShopeeID",
        "ShopName",
        "ShopURL",
        "Title",
        "Image",
        "URL",
        "Lalada_URL",
        "Tiki_URL",
        "Price",
        "Price_Min",
        "Price_Max",
        "Price_Min_BF",
        "Price_Max_BF",
        "Price_LAZ",
        "Price_Tiki",
        "Content",
        "Images_1",
        "Images_2",
        "Images_3",
        "Images_4",
        "Images_5",
        "Brand",
        "Category",
        "Stock",
        "Rating",
        "Like_Count",
        "Attributes",
        "Video",
        "Rating_Count",
        "Sold"
    ];
    }
    public function map($product): array
    {
        return [
            $product->id,
            $product->shopid,
            html_entity_decode($product->shopname),
            $product->shopurl,
            html_entity_decode($product->title),
            $product->image,
            $product->url."?utm_campaign=-&utm_content=web-aff---&utm_medium=affiliates&utm_source=an_17055730024&utm_term=am8qbf1gnyvf",
            $product->lazada,
            $product->tiki,
            $product->price,
            $product->price_min =$product->price_min,
            $product->price_max = $product->price_max,
            $product->price_min_bf =  $product->price_min_bf,
            $product->price_max_bf = $product->price_max_bf,
            $product->lazPrice,
            $product->tikiPrice,
            $product->content = $product->description,
            isset($product->images[0]) ? "https://down-vn.img.susercontent.com/file/".$product->images[0]   : "",
            isset($product->images[1]) ? "https://down-vn.img.susercontent.com/file/".$product->images[1]   : "",
            isset($product->images[2]) ? "https://down-vn.img.susercontent.com/file/".$product->images[2]   : "",
            isset($product->images[3]) ? "https://down-vn.img.susercontent.com/file/".$product->images[3]   : "",
            isset($product->images[4]) ? "https://down-vn.img.susercontent.com/file/".$product->images[4]   : "",
            $product->brand,
            html_entity_decode($product->category),
            $product->stock,

            $product->rating_star,
            $product->liked_count,
            html_entity_decode( json_encode($product->attributes)),
            $product->video,
            $product->rating,
            $product->sold,

        ];
    }

    function makeContent($contents) : string {
        $html = "";
        foreach ($contents as $content) {
            # code...

            if($content->type == 1){
                $html .='<img class=" content-img" src="https://down-vn.img.susercontent.com/file/'.$content->img_id.'" alt="">'."\n";
            }elseif($content->type == 0){
                $html .= '<p class="content-text">'.html_entity_decode($content->text).'</p>'."\n";
            }

        }
        return $html;
    }

    function makeImageUrl($images) {
        foreach ($images as $key => $image) {
            # code...
            $images[$key] = "https://down-vn.img.susercontent.com/file/".$image;
        }

        return implode('|',$images);
    }

}
