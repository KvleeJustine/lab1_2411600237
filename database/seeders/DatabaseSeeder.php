<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Create default admin user
        User::factory()->create([
            'name'     => 'Admin',
            'email'    => 'admin',
            'password' => Hash::make('admin123'),
        ]);

        // Seed products
        $this->call(ProductSeeder::class);
    }
}
