<?php
namespace App\Facade;

use Illuminate\Support\Facades\Facade;

class LazadaScraperFacade extends Facade{
    protected static function getFacadeAccessor()
    {
        return 'lazada';
    }
}
