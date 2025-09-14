<?php

use App\Http\Controllers\Employee\Api\v1\EmployeeApiController;
use App\Http\Controllers\MovieBooking\Api\v1\BookingController;
use App\Http\Controllers\MovieBooking\Api\v1\CinemaController;
use App\Http\Controllers\MovieBooking\Api\v1\MovieController;
use App\Http\Controllers\MovieBooking\Api\v1\ReservationSeatController;
use App\Http\Controllers\MovieBooking\Api\v1\RoomController;
use App\Http\Controllers\MovieBooking\Api\v1\SeatController;
use App\Http\Controllers\MovieBooking\Api\v1\ShowtimeController;
use App\Http\Controllers\MovieBooking\Api\v1\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('employee')->group(function () {
    Route::prefix('v1')->group(function () {
        Route::get('/employees', [EmployeeApiController::class, 'index']);
        Route::get('/employees/{id}', [EmployeeApiController::class, 'show']);
    });
});

Route::prefix('movie-booking')->group(function () {
    Route::prefix('v1')->group(function () {
        Route::apiResource('movies', MovieController::class);
        Route::apiResource('cinemas', CinemaController::class);
        Route::apiResource('rooms', RoomController::class);
        Route::apiResource('seats', SeatController::class);
        Route::apiResource('showtimes', ShowtimeController::class);
        Route::apiResource('bookings', BookingController::class);
        Route::apiResource('reservation-seats', ReservationSeatController::class);
        Route::apiResource('transactions', TransactionController::class);
    });
});
