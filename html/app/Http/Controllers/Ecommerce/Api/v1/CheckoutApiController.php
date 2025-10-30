<?php

namespace App\Http\Controllers\Ecommerce\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Ecommerce\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class CheckoutApiController extends Controller
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
        try {
            DB::connection('ecommerce')->beginTransaction();
            // Get cart items by order_id
            $cartItems = [
                [
                    'product_id' => 1,
                    'quantity' => 5,
                ],
                // [
                //     'product_id' => 2,
                //     'quantity' => 10,
                // ],
                // [
                //     'product_id' => 3,
                //     'quantity' => 15,
                // ],
            ];

            // Check stock
            $productIds = collect($cartItems)->pluck('product_id');
            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($cartItems as $item) {
                $product = $products[$item['product_id']];

                if ($product->stock < $item['quantity']) {
                    throw new Exception("product_id {$item['product_id']} out of stock");
                }
            }

            // Create order
            // Payment
            // Update stock
            foreach ($cartItems as $item) {
                $product = $products[$item['product_id']];
                $product->stock -= $item['quantity'];
                $product->save();
            }

            // Update order status
            DB::connection('ecommerce')->commit();

            Log::info("user_id {$request->user_id} order successfully");

            return response()->json([
                'status' => 'success',
                'message' => 'order successfully',
            ]);
        } catch (Throwable $th) {
            DB::rollBack();

            Log::error("user_id {$request->user_id} checkout error: {$th->getMessage()}");

            return response()->json([
                'status' => 'failed',
                'message' => 'create order failed',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
