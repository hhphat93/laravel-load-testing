<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockReservation;
use Illuminate\Support\Collection;

class OrderService
{
    public function createOrder($userId, $cartItems, $paymentMethod = 'cod')
    {
        $order = Order::create([
            'user_id' => $userId,
            'payment_method' => $paymentMethod ?? 'cod',
            'payment_status' => 'pending',
        ]);

        $orderItems = $cartItems->map(function ($item) use ($order) {
            return [
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
            ];
        })->toArray();

        OrderItem::insert($orderItems);
    }
}
