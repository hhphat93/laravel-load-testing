<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('movie_booking')->create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('phone_number', 15)->unique();
            $table->string('email')->nullable()->index();
            $table->string('password_hash');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('0: pending, 1: verified, 2: rejected');
            $table->timestamps();
            $table->index('status');
        });

        Schema::connection('movie_booking')->create('cinemas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('address', 500)->nullable();
            $table->timestamps();
        });

        Schema::connection('movie_booking')->create('rooms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cinema_id');
            $table->integer('total_seat')->default(0);
            $table->timestamps();
            $table->index('cinema_id');
        });

        Schema::connection('movie_booking')->create('seats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('room_id');
            $table->string('code');
            $table->integer('type')->default(0)->comment('normal, vip, couple');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('0: inactive, 1: active');
            $table->timestamps();
            $table->index('room_id');
        });

        Schema::connection('movie_booking')->create('movies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_url', 500);
            $table->unsignedInteger('duration_minutes');
            $table->dateTime('release_date')->nullable();
            $table->tinyInteger('status')->default(1)->comment('0: inactive, 1: active');
            $table->timestamps();
        });

        Schema::connection('movie_booking')->create('showtimes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('movie_id');
            $table->unsignedBigInteger('room_id');
            $table->dateTime('show_date');
            $table->decimal('price', 18, 2);
            $table->string('currency', 3)->default('VND');
            $table->tinyInteger('status')->default(1)->comment('0: inactive, 1: active');
            $table->timestamp('created_at')->useCurrent();
            $table->index('movie_id');
            $table->index('room_id');
            $table->index(['status', 'show_date']);
        });

        Schema::connection('movie_booking')->create('reservation_seats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('showtime_id');
            $table->unsignedBigInteger('seat_id');
            $table->enum('status', ['locked', 'reserved'])->default('locked');
            $table->dateTime('lock_expires_at')->nullable();
            $table->timestamps();
            $table->index('user_id');
            $table->unique(['showtime_id', 'seat_id']);
            $table->index('status');
        });

        Schema::connection('movie_booking')->create('bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('showtime_id');
            $table->decimal('total_amount', 18, 2)->unsigned();
            $table->string('currency', 3)->default('VND');
            $table->enum('method', ['bank_transfer', 'credit_card', 'paypal'])->default('bank_transfer');
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->timestamps();
            $table->index('user_id');
            $table->index('showtime_id');
            $table->index('method');
            $table->index('status');
        });

        Schema::connection('movie_booking')->create('booking_seats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('seat_id');
            $table->timestamps();
            $table->index('booking_id');
            $table->index('seat_id');
        });

        Schema::connection('movie_booking')->create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('booking_id');
            $table->string('transaction_code')->nullable();
            $table->decimal('amount', 18, 2);
            $table->string('currency', 3)->default('VND');
            $table->enum('method', ['bank_transfer', 'credit_card', 'paypal'])->default('bank_transfer');
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->text('response')->nullable()->comment('response from payment gateway');
            $table->timestamps();
            $table->index('booking_id');
            $table->index('transaction_code');
            $table->index('method');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('users');
        Schema::connection('movie_booking')->dropIfExists('cinemas');
        Schema::connection('movie_booking')->dropIfExists('rooms');
        Schema::connection('movie_booking')->dropIfExists('seats');
        Schema::connection('movie_booking')->dropIfExists('movies');
        Schema::connection('movie_booking')->dropIfExists('showtimes');
        Schema::connection('movie_booking')->dropIfExists('reservation_seats');
        Schema::connection('movie_booking')->dropIfExists('bookings');
        Schema::connection('movie_booking')->dropIfExists('booking_seats');
        Schema::connection('movie_booking')->dropIfExists('transactions');
    }
};
