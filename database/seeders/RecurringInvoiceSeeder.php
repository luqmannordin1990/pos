<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\Customer;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use App\Models\RecurringInvoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RecurringInvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        RecurringInvoice::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = Faker::create('ms_MY'); //  id_ID
        $frequencies = ['weekly', 'monthly', 'yearly', 'custom'];
        $statuses = ['active', 'on_hold', 'completed'];
        $customerIds = Customer::pluck('id')->toArray();
        $team = Team::first();
        foreach (range(1, 10) as $index) { // Create 10 fake recurring invoices
            $frequency = $faker->randomElement($frequencies);
            $startDate = $faker->date();
            $endDate = $faker->optional()->date(); // Nullable end date

            RecurringInvoice::create([
                'customer_id' => $faker->randomElement($customerIds),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'invoice_number' => strtoupper($faker->bothify('RINV-####')), // e.g., RINV-1234
                'frequency' => $frequency,
                'custom_cron' => $frequency === 'custom' ? '0 0 1 * *' : null, // Example cron if 'custom'
                'limit' => $faker->optional()->numberBetween(1, 12), // Nullable, max 12 invoices
                'status' => $faker->randomElement($statuses),
                'notes' => $faker->optional()->sentence(),
                'team_id' => $team->id,
            ]);
        }
    }
}
