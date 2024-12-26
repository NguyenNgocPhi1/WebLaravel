<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use Cart;

class CartController extends FrontendController
{
    
    
    public function __construct(
    ){
        parent::__construct();
    }

    public function checkout(){
        // Cart::instance('shopping')->destroy();
        $cart = Cart::instance('shopping')->content();
        dd($cart);
    }
}
