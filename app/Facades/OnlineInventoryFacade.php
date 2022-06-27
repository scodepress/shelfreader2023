<?php

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class OnlineInventoryFacade extends Facade {
    public static function getFacadeAccessor()
    {
        return 'onlineinventory';

    }
}