<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\Customer;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Customer::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = Faker::create('ms_MY'); //  id_ID

        $team = Team::first();

        foreach (range(1, 10) as $index) { // Generate 10 customers
            Customer::create([
                'name' => $faker->name(),
                'house_unit_no' => $faker->buildingNumber(),
                'telephone_no' => $this->generateMalaysianPhoneNumber(),
                'address' => $faker->address(),
                'email' => $faker->unique()->safeEmail(),
                'city_district' => $faker->city(),
                'ic_mykad' => $faker->unique()->numerify('###########'), // Example: 900101011234
                'postal_code' => $faker->postcode(),
                'date_of_birth' => $faker->date(),
                'gender' => $faker->randomElement(['Male', 'Female', 'Other']),
                'country' => 'Malaysia', // Default
                'notes_comments' => $faker->sentence(),
                'attachment' => $faker->randomElement([null, 'attachments/sample.jpg']),
                'team_id' => $team->id,
            ]);
        }
    }

    private function generateMalaysianPhoneNumber()
    {
        $prefixes = ['010', '011', '012', '013', '014', '016', '017', '018', '019']; // Common Malaysian prefixes
        $prefix = $prefixes[array_rand($prefixes)]; // Randomly pick one
        $number = $prefix . mt_rand(1000000, 9999999); // Generate last 7-8 digits

        return $number;
    }
}
