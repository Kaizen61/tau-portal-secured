<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Blog extends BaseController
{
    public function index(): string
    {
        return '<h2>A List of Blog Post</h2>';
    }


    public function createNew(){

        return view('blog_form');
    }

    public function saveBlog(){

        echo '<pre>';
        print_r($_POST);
        '</pre>';
    }



}
