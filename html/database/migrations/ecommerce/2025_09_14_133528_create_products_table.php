<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('ecommerce')->create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');                       // Tên sản phẩm
            $table->string('slug')->unique();             // Đường dẫn SEO
            $table->text('description')->nullable();      // Mô tả chi tiết
            $table->decimal('price', 15, 2);              // Giá bán
            $table->decimal('discount_price', 15, 2)->nullable(); // Giá khuyến mãi
            $table->unsignedInteger('stock')->default(0); // Số lượng tồn kho

            $table->string('sku')->unique()->nullable();  // Mã sản phẩm
            $table->string('image')->nullable();          // Ảnh chính
            $table->json('gallery')->nullable();          // Album ảnh

            $table->boolean('is_active')->default(true);  // Sản phẩm có đang active không
            $table->unsignedBigInteger('category_id')->nullable(); // liên kết category
            $table->unsignedBigInteger('brand_id')->nullable();    // liên kết brand

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('category_id');
            $table->index('brand_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('ecommerce')->dropIfExists('products');
    }
};
