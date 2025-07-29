<?php

namespace Database\Seeders;

use App\Models\Coin;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'samer',
            'email' => 'samer@live.com',
        ]);

        Coin::create([
            'name' => 'BITCOIN',
            'code' => 'BTC',
        ]);

        Coin::create([
            'name' => 'ETHEREUM',
            'code' => 'ETH',
        ]);
    }
}
