<?php

namespace App\Console\Commands;

use App\Models\Coin;
use App\Models\CryptoData;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;

class LabelCryptoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crypto:label-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Label crypto price data for machine learning';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $coins = Coin::where('code', '!=', 'USD')->get();
        $threshold = 0.0025; // 0.25%
        $lookAheadMinutes = 30;
        

        foreach ($coins as $c) {
            $btc_prices = CryptoData::where('coin', $c->name)->get();

            foreach ($btc_prices as $p) {
                $btc_price = $p->price;
                $nextPrice = $btc_prices->where('created_at', '>', $p->created_at)
                    ->where('created_at', '<=', Carbon::parse($p->created_at)->addMinutes($lookAheadMinutes))
                    ->last();

                if (!$nextPrice) {
                    continue;
                }
                $change = ($nextPrice->price - $btc_price) / $btc_price;

                if ($change >= $threshold) {
                    $p->label = 1;
                } else if ($change <= -$threshold) {
                    $p->label = -1;
                } else {
                    $p->label = 0;
                }
                echo $p->label;
                $p->save();
            }
        }
    }
}
