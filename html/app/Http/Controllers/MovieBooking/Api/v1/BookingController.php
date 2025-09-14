<?php

namespace App\Http\Controllers\MovieBooking\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\MovieBooking\Booking;
use App\Models\MovieBooking\BookingSeat;
use App\Models\MovieBooking\Movie;
use App\Models\MovieBooking\ReservationSeat;
use App\Models\MovieBooking\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $seat = ReservationSeat::where('user_id', '<>', $request->user_id)
            ->where('showtime_id', $request->showtime_id)
            ->where('seat_id', $request->seat_id)
            ->where(function ($query) {
                $query->where('status', 'reserved')
                    ->orWhere(function ($q) {
                        $q->where('status', 'locked')
                            ->where('lock_expires_at', '>', now());
                    });
            })
            ->exist();

        if ($seat) {
            Log::warning("Booking: Seat {$seat->seat_id}:{$seat->status} already reserved by user_id {$seat->user_id}");

            return response()->json(
                [
                    'message' => "Seat {$seat->seat_id}:{$seat->status} already reserved"
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        DB::transaction(function () use ($request) {
            $booking = Booking::create([
                'user_id' => $request->user_id,
                'showtime_id' => $request->showtime_id,
                'seat_id' => $request->seat_id,
                'method' => 'bank_transfer',
                'total_amount' => $request->total_amount,
                'currency' => 'VND',
                'status' => 'pending',
            ]);

            BookingSeat::create([
                'booking_id' => $booking->id,
                'seat_id' => $request->seat_id,
            ]);

            Transaction::create([
                'booking_id' => $booking->id,
                'amount' => $request->total_amount,
                'currency' => 'VND',
                'method' => 'bank_transfer',
                'status' => 'pending',
            ]);
        });

        Log::info("Booking created successfully: Seat {$seat->seat_id}");

        return response()->json([
            'message' => 'Booking created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        //
    }
}
