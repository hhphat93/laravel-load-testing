<?php

namespace App\Services;

use App\Models\Cart;

class CartService
{
    public function getCartItems($userId)
    {
        return Cart::where('user_id', $userId)->get();
    }

    public function emptyCart($userId)
    {
        Cart::where('user_id', $userId)->delete();
    }
}
