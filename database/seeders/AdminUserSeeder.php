<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin user already exists
        $existingAdmin = DB::table('users')->where('email', 'admin@example.com')->first();
        
        if (!$existingAdmin) {
            // Create admin user
            $userId = DB::table('users')->insertGetId([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'),
                'type' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Create profile for admin user
            DB::table('profiles')->insert([
                'user_id' => $userId,
                'phone' => '+1234567890',
                'address' => 'Admin Address',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            echo "Admin user created successfully!\n";
            echo "Email: admin@example.com\n";
            echo "Password: password123\n";
            echo "You can change these credentials after logging in.\n";
        } else {
            echo "Admin user already exists.\n";
        }
    }
}