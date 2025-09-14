<?php

namespace App\Http\Controllers\MovieBooking\Api\v1;

use App\Http\Controllers\Controller;

use App\Models\MovieBooking\BookingSeat;
use App\Models\MovieBooking\ReservationSeat;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ReservationSeatController extends Controller
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
        Log::debug("user_id {$request->user_id}");
        $cacheKey = "showtime:{$request->showtime_id};seat:{$request->seat_id}";

        if (Cache::has($cacheKey)) {
            $data = Cache::get($cacheKey);

            Log::warning("user_id {$request->user_id} Redis -> Seat {$request->seat_id} is {$data['status']} by user_id: {$data['user_id']}");

            return response()->json(
                [
                    'status' => $data['status'],
                    'message' => "Seat {$request->seat_id} already reserved"
                ],
                Response::HTTP_OK
            );
        }

        $lock = Cache::lock("showtime:{$request->showtime_id};seat:{$request->seat_id}", 5);

        try {
            if ($lock->get()) {
                // $lock->block(5);

                if (Cache::has($cacheKey)) {
                    $data = Cache::get($cacheKey);

                    Log::warning("user_id {$request->user_id} Redis -> Seat {$request->seat_id} is {$data['status']} by user_id: {$data['user_id']}");

                    return response()->json(
                        [
                            'status' => $data['status'],
                            'message' => "Seat {$request->seat_id} already reserved"
                        ],
                        Response::HTTP_OK
                    );
                }

                $seat = ReservationSeat::where('showtime_id', $request->showtime_id)
                    ->where('seat_id', $request->seat_id)
                    ->where(function ($query) {
                        $query->where('status', 'reserved')
                            ->orWhere(function ($q) {
                                $q->where('status', 'locked')
                                    ->where('lock_expires_at', '>', now());
                            });
                    })
                    ->first();

                if ($seat) {
                    Log::warning("user_id {$request->user_id} DB -> Seat {$seat->seat_id} is {$seat->status} by user_id: {$seat->user_id}");

                    return response()->json(
                        [
                            'status' => $seat->status,
                            'message' => "Seat {$request->seat_id} already reserved"
                        ],
                        Response::HTTP_OK
                    );
                }

                $expiredAt = Carbon::now()->addMinutes(10);
                $reservation = ReservationSeat::create([
                    'user_id' => $request->user_id,
                    'showtime_id' => $request->showtime_id,
                    'seat_id' => $request->seat_id,
                    'status' => 'locked',
                    'lock_expires_at' => $expiredAt
                ]);

                Cache::put($cacheKey, [
                    'status' => $reservation->status,
                    'user_id' => $reservation->user_id,
                ], $expiredAt);

                Log::info("Seat {$request->seat_id} reserved successfully by user_id: {$request->user_id}");
            } else {
                Log::alert("user_id {$request->user_id} can't get lock");

                return response()->json(
                    [
                        'message' => "Seat {$request->seat_id} already reserved"
                    ],
                    Response::HTTP_OK
                );
            }
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                Log::error("user_id {$request->user_id} DB -> Seat {$request->seat_id} duplicate entry");

                return response()->json(
                    [
                        'message' => "Seat {$request->seat_id} already reserved"
                    ],
                    Response::HTTP_OK
                );
            }
        } catch (Throwable $th) {
            Log::error("user_id {$request->user_id} DB -> Seat reservation failed", [
                'message' => $th->getMessage()
            ]);

            return response()->json(
                [
                    'message' => 'Seat reservation failed'
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        } catch (LockTimeoutException $th) {
            Log::error("user_id {$request->user_id} Redis -> user_id: {$request->user_id} lock timeout", [
                'message' => $th->getMessage()
            ]);

            return response()->json(
                [
                    'message' => 'Seat reservation failed'
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        } finally {
            $lock->release();
            Log::notice("user_id {$request->user_id} release lock");
        }

        return response()->json([
            'message' => 'Seat reserved successfully',
            'data' => $reservation ?? [],
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(BookingSeat $bookingSeat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BookingSeat $bookingSeat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BookingSeat $bookingSeat)
    {
        //
    }
}
