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

            for($i = 0; $i < $symbol_prices->count(); $i++) {
                // reset all labels
                // $symbol_prices[$i]->label = Null;
                // $symbol_prices[$i]->save();
                // echo $symbol_prices[$i]->label . ' ' . $i . '\n';
                // continue;

                // label against next 10 days
                $symbol_record = $symbol_prices[$i];
                $symbol_price = $symbol_record->avg_price;
                if($i + $lookAheadDays >= $symbol_prices->count()) continue;
                // get the next 10 days price
                $next10DaysPrice = $symbol_prices[$i + $lookAheadDays]->avg_price;

                $change = ($next10DaysPrice - $symbol_price) / $symbol_price;

                if ($change >= $threshold) {
                    $symbol_record->label = 1;
                } else if ($change <= -$threshold) {
                    $symbol_record->label = -1;
                } else {
                    $symbol_record->label = 0;
                }
                echo $symbol_record->label.' ' . $i . '\n';
                $symbol_record->save();

                // dump($next10DaysPrice);
            }
        }
    }
}
