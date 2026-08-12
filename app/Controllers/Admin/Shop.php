<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Shop extends BaseController
{
    public function index(): string
    {
        return '<h2>This is an Admin Shop Area</h2>';
    }


    public function product($type = null, $product_id = null){

        if($type===null){
            return view('product');
        }

        echo '<h2>This is an Admin product </h2>';
        //return view('product');
    }



}
