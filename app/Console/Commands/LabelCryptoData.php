<?php

namespace App\Console\Commands;

use App\Models\BinanceData;
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
        $symbols = ['BTCUSDT','ETHUSDT'];
        $threshold = 0.0050; // 0.5%
        $lookAheadDays = 10;
        
        foreach ($symbols as $symbol) {
            $symbol_prices = BinanceData::where('symbol', $symbol)->get();

            foreach ($symbol_prices as $p) {
                $symbol_price = $p->avg_price;
                $nextPrice = $symbol_prices->where('open_time', '>', $p->open_time)
                    ->where('open_time', '<=', Carbon::parse($p->open_time)->addDays($lookAheadDays))
                    ->last();

                if (!$nextPrice) {
                    continue;
                }
                $change = ($nextPrice->avg_price - $symbol_price) / $symbol_price;

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
