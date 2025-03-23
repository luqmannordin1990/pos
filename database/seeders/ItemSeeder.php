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
                'name' => $faker->word, // Random product name
                'price' => $faker->randomFloat(2, 10, 500), // Price between 10 and 500 MYR
                'unit' => $faker->randomElement(['Unit', 'Kg', 'Litre', 'Pcs', 'Set']), // Random unit
                'description' => $faker->sentence(10), // Random description
                'team_id' => $team->id,
            ]);
        }
    }
}
