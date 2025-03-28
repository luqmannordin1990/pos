<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Team;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds. 
     */
    public function run(): void
    {
        //
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Item::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = Faker::create('ms_MY'); //  id_ID

        $team = Team::first();
        foreach (range(1, 10) as $index) { // Generate 10 customers

            Item::create([
                'name' => $faker->word(),
                'info' => $faker->sentence(),
                'short_description' => $faker->sentence(),
                'price' => $faker->randomFloat(2, 10, 500), // Price between RM10 - RM500
                'cost_price' => $faker->randomFloat(2, 5, 400), // Cost price between RM5 - RM400
                'weight' => $faker->randomFloat(3, 0.1, 10), // Weight between 0.1kg - 10kg
                'order_limit' => $faker->numberBetween(1, 100),
                'current_stock_balance' => $faker->numberBetween(0, 500),
                'activate_ecommerce' => $faker->boolean(80), // 80% chance of being true
                'activate_stock_management' => $faker->boolean(70),
                'activate_product_variations' => $faker->boolean(60),
                'directory' => $faker->word(),
                'attachment' => json_encode([
                    'image' => $faker->imageUrl(640, 480, 'products', true),
                ]),
                'team_id' => $team->id,
            ]);
        }
    }
}
