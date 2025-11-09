<?php

namespace App\Http\Controllers\Api\v1;

use App\Exceptions\OutOfStockException;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class CartApiController  extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $cartItems = [
        //     [
        //         'user_id' => $userId,
        //         'product_id' => 1,
        //         'quantity' => 5,
        //     ],
        //     [
        //         'user_id' => $userId,
        //         'product_id' => 2,
        //         'quantity' => 10,
        //     ],
        // ];

        $userId = $request->user_id;

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock_available < $request->quantity) {
            throw new OutOfStockException('Product is out of stock', [
                'product_id' => $product->id,
                'stock_available' => $product->stock_available,
            ]);
        }

        $cart = Cart::updateOrCreate(
            ['user_id' => $userId, 'product_id' => $product->id],
            ['quantity' => $request->quantity]
        );

        Log::info("user_id: {$request->user_id} add items to cart successfully");

        return response()->json([
            'status' => 'success',
            'message' => 'add items to cart successfully',
            'data' => $cart,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
