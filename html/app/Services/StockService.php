<?php

namespace App\Services;

use App\Models\StockReservation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class StockService
{
    public function releaseStock($userId)
    {
        $stocks = StockReservation::where('user_id', $userId)->get();

        foreach ($stocks as $stock) {
            $stock->product->stock_available += $stock->quantity;
            $stock->product->save();
        }

        StockReservation::where('user_id', $userId)->delete();
    }

    public function reserveStock($userId, Collection $cartItems)
    {
        $stockReservations = $cartItems->map(function ($item) use ($userId) {
            return [
                'user_id' => $userId,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'status' => 'pending',
                'expires_at' => now()->addMinutes(5),
            ];
        })->toArray();

        StockReservation::insert($stockReservations);
    }

    public function updateStatus($userId, string $status)
    {
        StockReservation::where('user_id', $userId)
            ->where('status', 'pending')
            ->update(['status' => $status]);
    }
}
