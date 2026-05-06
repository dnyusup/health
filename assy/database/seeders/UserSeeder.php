<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['user_id' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'admin@spart.local',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        // Create sample user
        User::firstOrCreate(
            ['user_id' => 'user001'],
            [
                'name' => 'John Doe',
                'email' => 'john@spart.local',
                'password' => Hash::make('user123'),
                'role' => 'user',
            ]
        );
    }
}
