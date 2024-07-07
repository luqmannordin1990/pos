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
            RoleSeeder::class,
        ]);

        $user = User::factory()->create([
            'name' => 'admin',
            'username' => 'admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('U53r_4cc0un7'),
        ]);
        $user->assignRole(Role::where('name', 'admin')->first());

        $user = User::factory()->create([
            'name' => 'staff',
            'username' => 'staff',
            'email' => 'staff@test.com',
            'password' => Hash::make('U53r_4cc0un7'),
        ]);
        $user->assignRole(Role::where('name', 'staff')->first());

        
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
