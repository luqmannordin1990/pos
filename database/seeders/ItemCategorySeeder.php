<?php

namespace Database\Seeders;

use App\Models\Team;
use Faker\Factory as Faker;
use App\Models\ItemCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ItemCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ItemCategory::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        $faker = Faker::create('ms_MY'); //  id_ID

        $team = Team::first();
        foreach (range(1, 10) as $index) { // Generate 10 customers

            ItemCategory::create([
                'name' => $faker->word, // Random product name
                'team_id' => $team->id,
            ]);
        }
    }
}
