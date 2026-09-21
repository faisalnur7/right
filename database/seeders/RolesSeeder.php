<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $roles = ['Admin', 'Merchant', 'Customer', 'Affiliate_Customer', 'Prime_Affiliate_Customer'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        echo "Roles created successfully.\n";
    }
}
