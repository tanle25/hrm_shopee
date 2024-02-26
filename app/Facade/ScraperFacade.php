<?php
namespace App\Facade;

use Illuminate\Support\Facades\Facade;

class ScraperFacade extends Facade{
    protected static function getFacadeAccessor()
    {
        return 'getproduct';
    }
}
