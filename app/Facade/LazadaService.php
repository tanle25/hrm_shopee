<?php
namespace App\Facade;

use stdClass;
use Illuminate\Support\Str;
use Weidner\Goutte\GoutteFacade;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\DomCrawler\Crawler;

class LazadaService{
    public $url = 'https://www.lazada.vn/products/mau-moi-2023-set-do-nam-ao-tay-ngan-co-tron-phoi-quan-short-dui-tui-day-zenko-men-qa-085-i557514383-s1200924389.html';

    function getItem() {

        $html = file_get_contents($this->url);
        $crawler = new Crawler($html);
        // $crawler = GoutteFacade::request('GET', $this->url);

        $content = $crawler->filterXPath('//script[contains(.,"__moduleData__")]')->each(function($note){
            return $note->text();
        });
        $text = Str::between($content[0],'__moduleData__ =','; var __googleBot__ ');

        $images = $crawler->filter('.pdp-mod-common-image.item-gallery__thumbnail-image')->each(function($node){
            return $node->extract(array('src'));
        });

        // dd($images);

        $item = json_decode($text);
        $item->images = $images;
        $conver = mb_convert_encoding(json_encode($item),"UTF-8", "auto");
        // dd($conver);
        Storage::disk('public')->put('laz.html',$html);
        return $item;
    }
    function getContent() {
        // $html = file_get_contents($this->url);

        $item = $this->getItem();

        $content = Str::between($item->data->root->fields->product->desc,'<article>','</article>');
        return $content;
    }

    function getImages() {
        $item = $this->getItem();
        $images = collect($item->images)->each(function($img){
            return Str::replace('_120x120','_720x720',$img[0]);
        });
        dd($images);
    }

    function getVariants() {
        $item = $this->getItem()->data->root;
        $variants = $item->fields->productOption->skuBase->properties;
        return $variants;
    }

    function getPrice() {

    }
}
