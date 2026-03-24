<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VulnLabSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user — hardcoded credentials (intentional)
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@lab.com',
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        // 5 regular users
        User::factory(5)->create(['is_admin' => false]);

        // Seed some XSS-safe comments for the demo
        DB::table('comments')->insert([
            ['author' => 'Alice', 'body' => 'Great app!', 'created_at' => now(), 'updated_at' => now()],
            ['author' => 'Bob', 'body' => 'Works well for me.', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
