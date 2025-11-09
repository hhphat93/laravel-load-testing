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
        Schema::create('stock_reservations', function (Blueprint $table) {
            $table->id();
            $table->bigIncrements('user_id');
            $table->bigInteger('product_id');
            $table->integer('quantity')->default(1);
            $table->enum('status', ['pending', 'processing', 'confirmed'])->default('pending');
            $table->timestamp('expires_at');
            $table->timestamps();
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_reservations');
    }
};
