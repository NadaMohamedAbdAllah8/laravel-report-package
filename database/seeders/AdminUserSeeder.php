<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed a clear admin-like user for login/testing.
     */
    public function run(): void
    {
        $email = 'admin@reportpackage.test';

        User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Admin User',
                'password' => 'admin123',
            ]
        );
    }
}
