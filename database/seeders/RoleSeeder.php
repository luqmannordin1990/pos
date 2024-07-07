<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
 

        Permission::create(['name' => 'viewany']);
        
        $roleAdmin = Role::create(['name' => 'admin','guard_name' => 'web']);
        $roleAdmin->givePermissionTo(['viewany']);

        $roleAdmin = Role::create(['name' => 'staff','guard_name' => 'web']);
        $roleAdmin->givePermissionTo(['viewany']);

        // $roleAdmin = Role::create(['name' => 'ccd','guard_name' => 'web']);
        // $roleAdmin->givePermissionTo(['viewany']);

        // $roleAdmin = Role::create(['name' => 'ceo','guard_name' => 'web']);
        // $roleAdmin->givePermissionTo(['viewany']);

        // $roleExternal = Role::create(['name' => 'external','guard_name' => 'web']);
        // $roleExternal->givePermissionTo(['viewany']);
       

        
    
    }
}
