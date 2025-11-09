<?php

namespace App\Http\Controllers\Api\v1;

use App\Exceptions\OutOfStockException;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockReservation;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\StockService;
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

    public function confirm(Request $request) {
        $userId = $request->user_id;

        try {
            DB::beginTransaction();

            $stockService = new StockService();
            $stockService->releaseStock($userId);

            $cartItems = (new CartService())->getCartItems($userId);

            if ($cartItems->isEmpty()) {
                throw new Exception('Cart is empty');
            }

            $outOfstockProducts = [];

            foreach ($cartItems as $item) {
                $product = Product::where('id', $item->product_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($product->stock_available < $item['quantity']) {
                    $outOfstockProducts[] = [
                        'product_id' => $item->product_id,
                        'stock_available' => $product->stock_available,
                    ];
                } else {
                    $product->stock_available -= $item['quantity'];
                    $product->save();
                }
            }

            if ($outOfstockProducts) {
                throw new OutOfStockException('Some products are out of stock', $outOfstockProducts);
            }

            $stockService->reserveStock($userId, $cartItems);
            
            Log::info("user_id {$request->user_id} check stock successfully");

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'check stock successfully',
            ]);
            
        } catch (OutOfStockException $e) {
            DB::rollBack();

            Log::info("user_id {$userId} out of stock: " . json_encode($e->data));

            return response()->json([
                'message' => 'Out Of Stock',
                'detail' => $e->getMessage(),
                'data' => $e->data,
            ], Response::HTTP_BAD_REQUEST);
        } catch (Throwable $th) {
            DB::rollBack();

            Log::error("user_id {$userId} check stock error: {$th->getMessage()}");

            return response()->json([
                'status' => 'failed',
                'message' => 'check stock error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, CartService $cartService)
    {
        $userId = $request->user_id;

        try {
            DB::beginTransaction();

            $outOfstockProducts = [];
            $cartItems = $cartService->getCartItems($userId);

            if ($cartItems->isEmpty()) {
                throw new Exception('Cart is empty');
            }

            (new StockService())->updateStatus($userId, 'processing');

            foreach ($cartItems as $item) {
                $updated = DB::table('products')
                    ->where('id', $item->product_id)
                    ->whereRaw('stock >= ?', [$item->quantity])
                    ->update([
                        'stock' => DB::raw('stock - ' . $item->quantity),
                    ]);

                if ($updated === 0) {
                    $product = Product::where('id', $item->product_id)->firstOrFail();
                    $outOfstockProducts[] = [
                        'product_id' => $item->product_id,
                        'stock' => $product->stock,
                    ];
                }
            }

            if ($outOfstockProducts) {
                throw new OutOfStockException('Some products are out of stock', $outOfstockProducts);
            }

            (new OrderService())->createOrder($userId, $cartItems, $request->payment_method);
            (new StockService())->updateStatus($userId, 'confirmed');
            $cartService->emptyCart($userId);

            // Call payment gateway here (omitted for brevity)

            Log::info("user_id {$userId} create order successfully");

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'create order successfully',
            ]);
            
        } catch (OutOfStockException $e) {
            DB::rollBack();

            Log::info("user_id {$userId} out of stock: " . json_encode($e->data));

            return response()->json([
                'message' => 'Out Of Stock',
                'detail' => $e->getMessage(),
                'data' => $e->data,
            ], 400);
        } catch (Throwable $th) {
            DB::rollBack();

            Log::error("user_id {$userId} create order error: {$th->getMessage()}");

            return response()->json([
                'status' => 'failed',
                'message' => 'create order error',
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
