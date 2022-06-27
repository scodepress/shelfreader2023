<?php

namespace App\Contracts\Services\Api;


interface ApiServicesInterface 
{
    public function index($barcode);
    public function itemParameters($barcode);

}


