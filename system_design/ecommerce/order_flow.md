# Stock Reservation System - Tiki Style

## 📋 Tổng quan

Hệ thống đặt trước sản phẩm (Stock Reservation) giúp giải quyết **race condition** khi nhiều người mua cùng 1 sản phẩm cuối cùng.

### 🎯 Vấn đề cần giải quyết

**Scenario:** Sản phẩm còn 1 cái, 2 người cùng lúc:
1. Thêm vào giỏ hàng
2. Nhấn "Đặt hàng"
3. Thanh toán

→ **Race condition:** Cả 2 đều mua được! ❌

### ✅ Giải pháp: Reserve System

- User nhấn "Đặt hàng" → **Giữ stock trong 10 phút**
- Có thời gian thanh toán an toàn
- Hết thời gian → Tự động trả stock về

---

## 🏗️ Kiến trúc hệ thống

### Database Schema

#### 1. Bảng `products` (Thêm column mới)

```sql
ALTER TABLE products ADD COLUMN available_stock INT AFTER stock;
ALTER TABLE products ADD INDEX idx_available_stock (available_stock);

-- Sync data ban đầu
UPDATE products SET available_stock = stock;
```

**Giải thích:**
- `stock`: Tổng số hàng thực tế trong kho
- `available_stock`: Số hàng còn lại sau khi trừ đi phần đang được reserve

**Ví dụ:**
```
stock = 10 (tổng hàng trong kho)
available_stock = 7 (3 cái đang được reserve)
```

#### 2. Bảng `stock_reservations` (Mới)

```sql
CREATE TABLE stock_reservations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    order_id BIGINT UNSIGNED NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    status ENUM('reserved', 'confirmed', 'released') DEFAULT 'reserved',
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_product_status (product_id, status),
    INDEX idx_expires_status (expires_at, status),
    INDEX idx_user_status (user_id, status),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

**Status meanings:**
- `reserved`: Đang giữ stock (pending payment)
- `confirmed`: Đã thanh toán thành công
- `released`: Đã hủy hoặc hết hạn

#### 3. Bảng `orders` (Thêm column)

```sql
ALTER TABLE orders ADD COLUMN reservation_expires_at TIMESTAMP NULL;
```

---

## 💻 Implementation Code

### Step 1: Migration Files

#### `create_stock_reservations_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->enum('status', ['reserved', 'confirmed', 'released'])->default('reserved');
            $table->timestamp('expires_at');
            $table->timestamps();
            
            $table->index(['product_id', 'status']);
            $table->index(['expires_at', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_reservations');
    }
};
```

#### `add_available_stock_to_products.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('available_stock')->after('stock')->default(0);
            $table->index('available_stock');
        });

        // Sync existing data
        DB::statement('UPDATE products SET available_stock = stock');
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('available_stock');
        });
    }
};
```

#### `add_reservation_expires_to_orders.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('reservation_expires_at')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('reservation_expires_at');
        });
    }
};
```

---

### Step 2: Models

#### `app/Models/StockReservation.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class StockReservation extends Model
{
    protected $fillable = [
        'user_id',
        'order_id', 
        'product_id',
        'quantity',
        'status',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Helper methods
    public function isExpired()
    {
        return $this->expires_at->isPast() && $this->status === 'reserved';
    }

    // Query scopes
    public function scopeExpired($query)
    {
        return $query->where('status', 'reserved')
                    ->where('expires_at', '<', now());
    }

    public function scopeReserved($query)
    {
        return $query->where('status', 'reserved');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }
}
```

#### `app/Models/Product.php` (Thêm methods)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'stock',
        'available_stock',
        // ... other fields
    ];

    // Stock reservation methods
    public function reserveStock($quantity)
    {
        return $this->decrement('available_stock', $quantity);
    }

    public function releaseStock($quantity)
    {
        return $this->increment('available_stock', $quantity);
    }

    public function confirmStock($quantity)
    {
        return $this->decrement('stock', $quantity);
    }

    // Check if product has enough available stock
    public function hasAvailableStock($quantity)
    {
        return $this->available_stock >= $quantity;
    }

    // Relationship
    public function reservations()
    {
        return $this->hasMany(StockReservation::class);
    }
}
```

#### `app/Models/Order.php` (Thêm relationship)

```php
public function reservations()
{
    return $this->hasMany(StockReservation::class);
}
```

---

### Step 3: Service Layer

#### `app/Services/StockReservationService.php`

```php
<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockReservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class StockReservationService
{
    // Reservation duration in minutes
    const RESERVATION_MINUTES = 10;

    /**
     * Reserve stock for checkout
     * 
     * @param int $userId
     * @param array $cartItems [['product_id' => 1, 'quantity' => 2], ...]
     * @return array ['success' => bool, 'reservations' => [], 'message' => string]
     */
    public function reserveStock($userId, $cartItems)
    {
        return DB::transaction(function() use ($userId, $cartItems) {
            $reservations = [];
            
            foreach ($cartItems as $item) {
                // Lock product row to prevent race condition
                $product = Product::where('id', $item['product_id'])
                            ->lockForUpdate()
                            ->first();

                if (!$product) {
                    $this->rollbackReservations($reservations);
                    return [
                        'success' => false,
                        'message' => 'Sản phẩm không tồn tại'
                    ];
                }

                // Check available stock
                if ($product->available_stock < $item['quantity']) {
                    // Rollback all reservations made so far
                    $this->rollbackReservations($reservations);
                    
                    return [
                        'success' => false,
                        'message' => "Sản phẩm '{$product->name}' chỉ còn {$product->available_stock} cái",
                        'product_name' => $product->name,
                        'available' => $product->available_stock,
                        'requested' => $item['quantity']
                    ];
                }

                // Reserve stock (decrease available_stock)
                $product->reserveStock($item['quantity']);

                // Create reservation record
                $reservation = StockReservation::create([
                    'user_id' => $userId,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'status' => 'reserved',
                    'expires_at' => now()->addMinutes(self::RESERVATION_MINUTES)
                ]);

                $reservations[] = $reservation;

                Log::info('Stock reserved', [
                    'user_id' => $userId,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'reservation_id' => $reservation->id
                ]);
            }

            return [
                'success' => true,
                'reservations' => $reservations,
                'expires_at' => now()->addMinutes(self::RESERVATION_MINUTES),
                'message' => 'Đơn hàng đã được giữ trong ' . self::RESERVATION_MINUTES . ' phút'
            ];
        });
    }

    /**
     * Confirm reservation after successful payment
     * 
     * @param int $orderId
     * @param array $reservationIds
     * @return bool
     */
    public function confirmReservation($orderId, $reservationIds)
    {
        return DB::transaction(function() use ($orderId, $reservationIds) {
            $reservations = StockReservation::whereIn('id', $reservationIds)
                                ->where('status', 'reserved')
                                ->lockForUpdate()
                                ->get();

            if ($reservations->isEmpty()) {
                throw new \Exception('No reservations found');
            }

            foreach ($reservations as $reservation) {
                // Check if expired
                if ($reservation->isExpired()) {
                    throw new \Exception('Reservation has expired. Please try again.');
                }

                // Confirm: decrease actual stock
                $product = Product::lockForUpdate()->find($reservation->product_id);
                $product->confirmStock($reservation->quantity);

                // Update reservation status
                $reservation->update([
                    'status' => 'confirmed',
                    'order_id' => $orderId
                ]);

                Log::info('Reservation confirmed', [
                    'reservation_id' => $reservation->id,
                    'order_id' => $orderId,
                    'product_id' => $product->id
                ]);
            }

            return true;
        });
    }

    /**
     * Release reservation (cancel or timeout)
     * 
     * @param int $reservationId
     * @return bool
     */
    public function releaseReservation($reservationId)
    {
        return DB::transaction(function() use ($reservationId) {
            $reservation = StockReservation::where('id', $reservationId)
                            ->where('status', 'reserved')
                            ->lockForUpdate()
                            ->first();

            if (!$reservation) {
                return false;
            }

            // Release stock back to available_stock
            $product = Product::lockForUpdate()->find($reservation->product_id);
            $product->releaseStock($reservation->quantity);

            // Update reservation status
            $reservation->update(['status' => 'released']);

            Log::info('Reservation released', [
                'reservation_id' => $reservation->id,
                'product_id' => $product->id,
                'quantity' => $reservation->quantity
            ]);

            return true;
        });
    }

    /**
     * Release multiple reservations
     * 
     * @param array $reservationIds
     * @return int Number of released reservations
     */
    public function releaseReservations($reservationIds)
    {
        $count = 0;
        foreach ($reservationIds as $id) {
            if ($this->releaseReservation($id)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Release all user's expired reservations
     * 
     * @param int|null $userId
     * @return int Number of released reservations
     */
    public function releaseExpiredReservations($userId = null)
    {
        $query = StockReservation::expired();
        
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $expiredReservations = $query->get();

        $count = 0;
        foreach ($expiredReservations as $reservation) {
            if ($this->releaseReservation($reservation->id)) {
                $count++;
            }
        }

        if ($count > 0) {
            Log::info("Released {$count} expired reservations");
        }

        return $count;
    }

    /**
     * Rollback reservations (helper for transaction)
     * 
     * @param array $reservations
     */
    private function rollbackReservations($reservations)
    {
        foreach ($reservations as $reservation) {
            $this->releaseReservation($reservation->id);
        }
    }

    /**
     * Get user's active reservations
     * 
     * @param int $userId
     * @return Collection
     */
    public function getUserActiveReservations($userId)
    {
        return StockReservation::where('user_id', $userId)
                    ->where('status', 'reserved')
                    ->where('expires_at', '>', now())
                    ->with('product')
                    ->get();
    }
}
```

---

### Step 4: Controller

#### `app/Http/Controllers/Api/CheckoutController.php`

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StockReservationService;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    protected $reservationService;

    public function __construct(StockReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    /**
     * Step 1: User clicks "Đặt hàng" button
     * Reserve stock and create pending order
     * 
     * POST /api/checkout/create-order
     */
    public function createOrder(Request $request)
    {
        $userId = auth()->id();
        
        // Get cart items
        $cartItems = Cart::where('user_id', $userId)
                        ->with('product')
                        ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Giỏ hàng trống'
            ], 400);
        }

        // Prepare items for reservation
        $itemsToReserve = $cartItems->map(function($item) {
            return [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price
            ];
        })->toArray();

        // Reserve stock
        $result = $this->reservationService->reserveStock($userId, $itemsToReserve);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        // Calculate total
        $total = collect($itemsToReserve)->sum(function($item) {
            return $item['price'] * $item['quantity'];
        });

        // Create pending order
        $order = Order::create([
            'user_id' => $userId,
            'total' => $total,
            'status' => 'pending',
            'reservation_expires_at' => $result['expires_at']
        ]);

        // Link reservations to order
        foreach ($result['reservations'] as $reservation) {
            $reservation->update(['order_id' => $order->id]);
        }

        // Create order items
        foreach ($itemsToReserve as $item) {
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);
        }

        // Clear cart
        Cart::where('user_id', $userId)->delete();

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data' => [
                'order_id' => $order->id,
                'total' => $order->total,
                'expires_at' => $result['expires_at'],
                'expires_in_seconds' => now()->diffInSeconds($result['expires_at']),
                'reservation_ids' => $result['reservations']->pluck('id')
            ]
        ], 201);
    }

    /**
     * Step 2: Payment completed successfully
     * Confirm reservations and finalize order
     * 
     * POST /api/orders/{order}/confirm-payment
     */
    public function confirmPayment(Request $request, $orderId)
    {
        $order = Order::with('reservations')->findOrFail($orderId);

        // Verify ownership
        if ($order->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Check if order is already paid
        if ($order->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Order already paid'
            ], 400);
        }

        try {
            $reservationIds = $order->reservations()->pluck('id')->toArray();
            
            if (empty($reservationIds)) {
                throw new \Exception('No reservations found for this order');
            }

            // Confirm reservations (will decrease actual stock)
            $this->reservationService->confirmReservation($orderId, $reservationIds);

            // Update order status
            $order->update(['status' => 'paid']);

            return response()->json([
                'success' => true,
                'message' => 'Thanh toán thành công',
                'data' => $order->fresh()
            ]);

        } catch (\Exception $e) {
            // Payment failed or expired - release stock
            $this->cancelOrder($orderId);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Cancel order - release reservations
     * 
     * POST /api/orders/{order}/cancel
     */
    public function cancelOrder($orderId)
    {
        $order = Order::with('reservations')->findOrFail($orderId);

        // Verify ownership
        if ($order->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Check if order can be cancelled
        if ($order->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel paid order'
            ], 400);
        }

        // Release all reserved stock
        $reservationIds = $order->reservations()
                                ->where('status', 'reserved')
                                ->pluck('id')
                                ->toArray();

        $released = $this->reservationService->releaseReservations($reservationIds);

        // Update order status
        $order->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Đơn hàng đã được hủy',
            'data' => [
                'order_id' => $order->id,
                'released_reservations' => $released
            ]
        ]);
    }

    /**
     * Check reservation status and remaining time
     * 
     * GET /api/orders/{order}/reservation-status
     */
    public function checkReservation($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Verify ownership
        if ($order->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $timeRemaining = now()->diffInSeconds($order->reservation_expires_at, false);
        $isExpired = $timeRemaining <= 0;

        // Auto-cancel if expired
        if ($isExpired && $order->status === 'pending') {
            $this->cancelOrder($orderId);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'order_id' => $order->id,
                'status' => $order->status,
                'expires_at' => $order->reservation_expires_at,
                'time_remaining_seconds' => max(0, $timeRemaining),
                'is_expired' => $isExpired
            ]
        ]);
    }

    /**
     * Get user's active reservations
     * 
     * GET /api/my-reservations
     */
    public function getMyReservations()
    {
        $reservations = $this->reservationService->getUserActiveReservations(auth()->id());

        return response()->json([
            'success' => true,
            'data' => $reservations
        ]);
    }
}
```

---

### Step 5: Routes

#### `routes/api.php`

```php
<?php

use App\Http\Controllers\Api\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    
    // Checkout & Reservation APIs
    Route::prefix('checkout')->group(function () {
        Route::post('/create-order', [CheckoutController::class, 'createOrder']);
    });

    Route::prefix('orders')->group(function () {
        Route::post('/{order}/confirm-payment', [CheckoutController::class, 'confirmPayment']);
        Route::post('/{order}/cancel', [CheckoutController::class, 'cancelOrder']);
        Route::get('/{order}/reservation-status', [CheckoutController::class, 'checkReservation']);
    });

    // User's reservations
    Route::get('/my-reservations', [CheckoutController::class, 'getMyReservations']);
});
```

---

### Step 6: Console Command (Cron Job)

#### `app/Console/Commands/ReleaseExpiredReservations.php`

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\StockReservationService;

class ReleaseExpiredReservations extends Command
{
    protected $signature = 'reservations:release-expired';
    
    protected $description = 'Release expired stock reservations and restore available stock';

    public function handle(StockReservationService $service)
    {
        $this->info('Checking for expired reservations...');
        
        $count = $service->releaseExpiredReservations();
        
        if ($count > 0) {
            $this->info("✓ Released {$count} expired reservations");
        } else {
            $this->info('✓ No expired reservations found');
        }

        return Command::SUCCESS;
    }
}
```

#### `app/Console/Kernel.php`

```php
<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Run every minute to release expired reservations
        $schedule->command('reservations:release-expired')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
```

---

## 🚀 Setup Instructions

### 1. Run Migrations

```bash
php artisan migrate
```

### 2. Enable Laravel Scheduler

**Option A: Using Crontab (Production)**

```bash
# Edit crontab
crontab -e

# Add this line:
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

**Option B: Using Supervisor (Recommended)**

Create file `/etc/supervisor/conf.d/laravel-scheduler.conf`:

```ini
[program:laravel-scheduler]
process_name=%(program_name)s
command=/bin/bash -c "while true; do php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1; sleep 60; done"
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/path-to-your-project/storage/logs/scheduler.log
```

Then:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-scheduler
```

### 3. Test Manually

```bash
# Test the command directly
php artisan reservations:release-expired
```

---

## 📊 Flow Diagram

```
User clicks "Đặt hàng"
         ↓
┌────────────────────────┐
│  Reserve Stock         │
│  - Lock product row    │
│  - Check available     │
│  - Decrease available  │
│  - Create reservation  │
└────────────────────────┘
         ↓
┌────────────────────────┐
│  Create Pending Order  │
│  - Status: pending     │
│  - Expires in 10 mins  │
└────────────────────────┘
         ↓
    ┌────┴────┐
    │         │
Payment    Cancel/Timeout
Success       ↓
    ↓     Release Stock
Confirm   (available++)
Stock        ↓
(stock--)  Status: cancelled
    ↓
Status: paid
```

---

## 🧪 Testing APIs

### 1. Create Order (Reserve Stock)

```bash
POST /api/checkout/create-order
Authorization: Bearer {token}

Response:
{
  "success": true,
  "message": "Đơn hàng đã được giữ trong 10 phút",
  "data": {
    "order_id": 123,
    "total": 500000,
    "expires_at": "2024-01-01 10:10:00",
    "expires_in_seconds": 600,
    "reservation_ids": [1, 2, 3]
  }
}
```

### 2. Check Reservation Status

```bash
GET /api/orders/123/reservation-status
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": {
    "order_id": 123,
    "status": "pending",
    "expires_at": "2024-01-01 10:10:00",
    "time_remaining_seconds": 425,
    "is_expired": false
  }
}
```

### 3. Confirm Payment

```bash
POST /api/orders/123/confirm-payment
Authorization: Bearer {token}

Response:
{
  "success": true,
  "message": "Thanh toán thành công",
  "data": {
    "id": 123,
    "status": "paid",
    "total": 500000,
    ...
  }
}
```

### 4. Cancel Order

```bash
POST /api/orders/123/cancel
Authorization: Bearer {token}

Response:
{
  "success": true,
  "message": "Đơn hàng đã được hủy",
  "data": {
    "order_id": 123,
    "released_reservations": 3
  }
}
```

---

## 🎨 Frontend Integration

### Countdown Timer Example (Vue.js)

```vue
<template>
  <div v-if="order.status === 'pending'">
    <div class="countdown">
      <i class="clock-icon"></i>
      <span v-if="!isExpired">
        Còn {{ timeRemaining }} để hoàn tất thanh toán
      </span>
      <span v-else class="expired">
        Đơn hàng đã hết hạn
      </span>
    </div>
    <button @click="confirmPayment" :disabled="isExpired">
      Thanh toán ngay
    </button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      order: {},
      timeRemaining: '',
      isExpired: false,
      intervalId: null
    }
  },
  mounted() {
    this.startCountdown();
  },
  methods: {
    async checkReservationStatus() {
      const response = await axios.get(
        `/api/orders/${this.order.id}/reservation-status`