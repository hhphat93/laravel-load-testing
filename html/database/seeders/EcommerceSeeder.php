<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class EcommerceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Products
        $faker = Faker::create();

        $data = [];

        for ($i = 0; $i < 100; $i++) {
            $name = $faker->words(3, true);

            $data[] = [
                'name'           => ucfirst($name),
                'slug'           => Str::slug($name) . '-' . $faker->unique()->numberBetween(1, 100000),
                'description'    => $faker->paragraph(5),
                'price'          => $faker->randomFloat(2, 10000, 5000000),
                'discount_price' => $faker->boolean(30)
                                        ? $faker->randomFloat(2, 5000, 2000000)
                                        : null,
                'stock'          => $faker->numberBetween(0, 200),
                'sku'            => strtoupper(Str::random(8)),
                // 'image'          => $faker->imageUrl(600, 600, 'product', true, 'Faker'),
                // 'gallery'        => json_encode([
                //                         $faker->imageUrl(600, 600, 'product'),
                //                         $faker->imageUrl(600, 600, 'product'),
                //                     ]),
                'is_active'      => $faker->boolean(80),
                'category_id'    => $faker->numberBetween(1, 10),
                'brand_id'       => $faker->numberBetween(1, 5),
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        // insert bulk cho nhanh
        DB::connection('ecommerce')->table('products')->insert($data);
    }
}
