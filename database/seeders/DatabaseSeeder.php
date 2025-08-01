<?php

namespace Database\Seeders;

use App\Models\Account;
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
            'name' => 'DOLAR',
            'code' => 'USD',
        ]);

        Coin::create([
            'name' => 'ETHEREUM',
            'code' => 'ETH',
        ]);

        Account::create([
            'user_id' => 1,
            'coin_id' => 1,
            'balance' => 1,
        ]);

        Account::create([
            'user_id' => 1,
            'coin_id' => 2,
            'balance' => 1,
        ]);

        Account::create([
            'user_id' => 1,
            'coin_id' => 3,
            'balance' => 100,
        ]);
    }
}
