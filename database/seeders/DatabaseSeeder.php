<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // RoleSeeder::class,
            TeamSeeder::class,
            UserSeeder::class,

            CustomerSeeder::class,
            ItemSeeder::class,
            EstimateSeeder::class,
            InvoiceSeeder::class,
            RecurringInvoiceSeeder::class,
            PaymentSeeder::class,
            ExpenseCategorySeeder::class,
            ExpenseSeeder::class,
            TaxSeeder::class,
        ]);




        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
