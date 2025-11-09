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
        Schema::create('carts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->nullable();
            $table->string('session_id', 100)->nullable(); //for guest users
            $table->bigInteger('product_id');
            $table->bigInteger('quantity')->default(1);
            $table->decimal('price', 12, 2);
            $table->storedAs('quantity * price');
            $table->timestamps();
            $table->index('user_id');
            $table->index('session_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carts');
    }
};
