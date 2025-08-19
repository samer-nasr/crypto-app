<?php

namespace App\Console\Commands;

use App\Http\Helper\DataHelper;
use App\Models\BinanceData;
use Illuminate\Console\Command;

class CreateDataFeatures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crypto:create-features';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create data features';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $symbols = ['BTCUSDT', 'ETHUSDT'];

        foreach ($symbols as $symbol) {
            $records = BinanceData::where('symbol', $symbol)->get();
            $close_prices = array_column($records->toArray(), 'close_price');
            try {
                for ($i = 1; $i < $records->count(); $i++) {
                    $record = $records[$i];
                   
                    $previous_record = $records[$i - 1];

                    $dataHelper = new DataHelper($record, $previous_record, $records, $close_prices);

                    // Calculate average price
                    $avg_price              = $dataHelper->getAveragePrice();
                    // Calculate Price range
                    $price_range            = $dataHelper->getPriceRange();
                    // Calculate Percentage change
                    $percentage_change      = $dataHelper->getPercentageChange();
                    // Calculate previous average price
                    $previous_avg_price     = $dataHelper->getPreviousAveragePrice();
                    // Calculate previous price range
                    $previous_price_change  = $dataHelper->getPreviousPercentageChange();
                    // Calculate sma 5 , 10 , 20, 50
                    $sma_5                  = $dataHelper->getSma(5, $i);
                    $sma_10                 = $dataHelper->getSma(10, $i);
                    $sma_20                 = $dataHelper->getSma(20, $i);
                    $sma_50                 = $dataHelper->getSma(50, $i);
                    // Calculate ema 5 , 10 , 20, 50
                    $ema_5                  = $dataHelper->getEma(5, $i);
                    $ema_10                 = $dataHelper->getEma(10, $i);
                    $ema_20                 = $dataHelper->getEma(20, $i);
                    $ema_50                 = $dataHelper->getEma(50, $i);

                    // calculate rsi 14
                    $rsi_14                 = $dataHelper->getRsi14($i, 14);

                    $record->avg_price              = $avg_price;
                    $record->price_range            = $price_range;
                    $record->percentage_change      = $percentage_change;
                    $record->previous_avg_price     = $previous_avg_price;
                    $record->previous_price_change  = $previous_price_change;
                    $record->sma_5                  = $sma_5;
                    $record->sma_10                 = $sma_10;
                    $record->sma_20                 = $sma_20;
                    $record->sma_50                 = $sma_50;
                    $record->ema_5                  = $ema_5;
                    $record->ema_10                 = $ema_10;
                    $record->ema_20                 = $ema_20;
                    $record->ema_50                 = $ema_50;
                    $record->rsi_14                 = $rsi_14;

                    $record->save();
                    echo "Record saved: " . $record->open_time . "\n";
                }
            } catch (\Exception $e) {
                dd($e);
            }
        }
    }
}
