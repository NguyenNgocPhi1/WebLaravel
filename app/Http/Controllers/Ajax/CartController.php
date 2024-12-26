<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\FrontendController;
use App\Services\Interfaces\CartServiceInterface as CartService;
use Illuminate\Http\Request;
use Cart;


class CartController extends FrontendController
{
    protected $cartService;

    public function __construct(
        CartService $cartService
    ){
        parent::__construct();
        $this->cartService = $cartService;
    }

    public function create(Request $request){
        $flag = $this->cartService->create($request, $this->language);
        $cart = Cart::instance('shopping')->content();
        return response()->json([
            'cart' => $cart, 
            'messages' => 'Thêm sản phẩm vào giỏ hàng thành công',
            'code' => ($flag) ? 10 : 11
        ]);
    }
}
