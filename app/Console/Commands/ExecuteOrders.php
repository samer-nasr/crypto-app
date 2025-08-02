<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Coin;
use App\Models\CryptoData;
use App\Models\MongoLog;
use App\Models\Order;
use Illuminate\Console\Command;

class ExecuteOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:execute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for matching prices and execute orders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // get all coins
        $coins = Coin::where('code', '!=', 'USD')->get();
        // Get latest price document
        $latest_prices = [];
        foreach ($coins as $c) {
            $coin_price = CryptoData::where('coin', strtoupper($c->name))
                ->latest()
                ->first();
            $latest_prices[$c->code] = $coin_price->price;
        }

        // Get all pending orders
        $orders = Order::with('coin', 'user.accounts')
            ->where('status', 'pending')
            ->where('is_deleted', 0)
            ->get();


        foreach ($orders as $o) {
            $log = [];
            $type = $o->type;
            $account            = $o->user->accounts->where('coin_id', $o->coin_id)->first();
            $counter_account    = $o->user->accounts->where('coin_id', $o->counter_coin_id)->first();
            $log['old'] = [
                'account' => $account->balance,
                'counter_account' => $counter_account->balance
            ];
            if ($type == 'Buy') {
                if ($latest_prices[$o->coin->code] <= $o->price) {
                    $o->status = 'completed';
                    $o->save();

                    $account->balance += $o->quantity;
                    $account->save();

                    $counter_account->balance -= $o->counter_price;
                    $counter_account->save();

                    $log['new'] = [
                        'account' => $account->balance,
                        'counter_account' => $counter_account->balance
                    ];
                }
            } else {
                if ($latest_prices[$o->coin->code] >= $o->price) {
                    $o->status = 'completed';
                    $o->save();

                    $account->balance -= $o->quantity;
                    $account->save();

                    $counter_account->balance += $o->counter_price;
                    $counter_account->save();

                    $log['new'] = [
                        'account' => $account->balance,
                        'counter_account' => $counter_account->balance
                    ];
                }
            }
            if (isset($log['old']) && isset($log['new'])) {
                MongoLog::create([
                    'event' => 'order_success',
                    'order_type' => $type,
                    'old_account' => $log['old'],
                    'new_account' => $log['new'],
                    'logged_at' => now(),
                ]);
            }
        }
    }
}
