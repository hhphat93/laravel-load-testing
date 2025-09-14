<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MovieBookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Users
        DB::connection('movie_booking')->table('users')->insert([
            [
                'name' => 'Nguyen Van A',
                'phone_number' => '0900000001',
                'email' => 'a@example.com',
                'password_hash' => bcrypt('password'),
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tran Thi B',
                'phone_number' => '0900000002',
                'email' => 'b@example.com',
                'password_hash' => bcrypt('password'),
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Cinemas
        DB::connection('movie_booking')->table('cinemas')->insert([
            [
                'name' => 'CGV Vincom',
                'address' => 'Vincom Center, District 1, HCMC',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lotte Cinema',
                'address' => 'Lotte Mart, District 7, HCMC',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Rooms
        DB::connection('movie_booking')->table('rooms')->insert([
            [
                'cinema_id' => 1,
                'total_seat' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'cinema_id' => 2,
                'total_seat' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Seats
        foreach (range(1, 50) as $i) {
            DB::connection('movie_booking')->table('seats')->insert([
                'room_id' => 1,
                'code' => 'A' . $i,
                'type' => $i % 3, // 0: normal, 1: vip, 2: couple
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        foreach (range(1, 40) as $i) {
            DB::connection('movie_booking')->table('seats')->insert([
                'room_id' => 2,
                'code' => 'B' . $i,
                'type' => $i % 2, // 0: normal, 1: vip
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Movies
        DB::connection('movie_booking')->table('movies')->insert([
            [
                'title' => 'Avengers: Endgame',
                'description' => 'Superhero movie',
                'image_url' => 'https://example.com/avengers.jpg',
                'duration_minutes' => 180,
                'release_date' => now()->subYear(),
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Conan Movie',
                'description' => 'Detective anime movie',
                'image_url' => 'https://example.com/conan.jpg',
                'duration_minutes' => 120,
                'release_date' => now()->subMonths(6),
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Showtimes
        DB::connection('movie_booking')->table('showtimes')->insert([
            [
                'movie_id' => 1,
                'room_id' => 1,
                'show_date' => now()->addDays(1)->setTime(19, 0),
                'price' => 120000,
                'status' => 1,
                'created_at' => now(),
            ],
            [
                'movie_id' => 2,
                'room_id' => 2,
                'show_date' => now()->addDays(2)->setTime(20, 0),
                'price' => 100000,
                'status' => 1,
                'created_at' => now(),
            ],
        ]);
    }
}
