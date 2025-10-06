<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database migrations.
     */
    public function run(): void
    {
        // Create an admin user
        User::updateOrCreate(
            ['email' => 'admin@hcrm.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@hcrm.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'department' => 'Administration',
                'is_active' => true,
            ]
        );

        // Create a sales rep user
        User::updateOrCreate(
            ['email' => 'sales@hcrm.com'],
            [
                'name' => 'Sales Representative',
                'email' => 'sales@hcrm.com',
                'password' => Hash::make('password'),
                'role' => 'sales_rep',
                'department' => 'Sales',
                'is_active' => true,
            ]
        );

        // Create a department manager user
        User::updateOrCreate(
            ['email' => 'manager@hcrm.com'],
            [
                'name' => 'Department Manager',
                'email' => 'manager@hcrm.com',
                'password' => Hash::make('password'),
                'role' => 'department_manager',
                'department' => 'Technology',
                'is_active' => true,
            ]
        );
    }
}