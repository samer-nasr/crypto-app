<?php

namespace App\Console\Commands;

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
            try {
                for ($i = 1; $i < $records->count(); $i++) {
                    
                    $record = $records[$i];
                    // if data is filled skip
                    // if($record->avg_price) continue;
                    // if($record->ema_50) continue;
                    $previous_record = $records[$i - 1];
                    // dd($record->toArray() , $previous_record->toArray());

                    // Calculate average price
                    $avg_price = ($record->open_price + $record->close_price + $record->high_price + $record->low_price) / 4;

                    // Calculate Price range
                    $price_change = $record->high_price - $record->low_price;

                    // Calculate Percentage change
                    $percentage_change = ($record->close_price - $record->open_price) / $record->open_price;

                    // Calculate previous average price
                    $previous_avg_price = ($previous_record->open_price + $previous_record->close_price + $previous_record->high_price + $previous_record->low_price) / 4;

                    // Calculate previous price range
                    $previous_price_change = ($previous_record->close_price - $previous_record->open_price) / $previous_record->open_price;

                    // calculate the sma 5 and ema 5
                    if($i >= 4)
                    {
                        // calculate the sma 5
                        $sma_5_sum = 0;
                        // get the sum of 5 past records
                        for ($j = 0; $j < 5; $j++) 
                        {
                            $sma_5_sum += $records[$i - $j]->close_price;
                        }
                        $sma_5 = $sma_5_sum / 5;
                        $record->sma_5 = $sma_5;

                        // calculate ema 5
                        if($i == 4)
                        {
                            $ema_5 = $sma_5;
                        } else {
                            $k = 2 / (5 + 1);
                            $ema_5 = ($k * $record->close_price) + ( (1 - $k) * $previous_record->ema_5 );
                        }
                        $record->ema_5 = $ema_5;
                    }

                    // calculate the sma 10 and ema 10
                    if($i >= 9)
                    {
                        // calculate the sma 10
                        $sma_10_sum = 0;
                        // get the sum of 10 past records
                        for ($j = 0; $j < 10; $j++) 
                        {
                            $sma_10_sum += $records[$i - $j]->close_price;
                        }
                        $sma_10 = $sma_10_sum / 10;
                        $record->sma_10 = $sma_10;

                        // calculate ema 10
                        if($i == 9)
                        {
                            $ema_10 = $sma_10;
                        } else {
                            $k = 2 / (10 + 1);
                            $ema_10 = ($k * $record->close_price) + ( (1 - $k) * $previous_record->ema_10 );
                        }
                        $record->ema_10 = $ema_10;
                    }

                    // calculate the sma 20 and ema 20
                    if($i >= 19)
                    {
                        // calculate the sma 20
                        $sma_20_sum = 0;
                        // get the sum of 20 past records
                        for ($j = 0; $j < 20; $j++) 
                        {
                            $sma_20_sum += $records[$i - $j]->close_price;
                        }
                        $sma_20 = $sma_20_sum / 10;
                        $record->sma_20 = $sma_20;

                        // calculate ema 20
                        if($i == 19)
                        {
                            $ema_20 = $sma_20;
                        } else {
                            $k = 2 / (20 + 1);
                            $ema_20 = ($k * $record->close_price) + ( (1 - $k) * $previous_record->ema_20 );
                        }
                        $record->ema_20 = $ema_20;
                    }

                    // calculate the sma 50 and ema 50
                    if($i >= 49)
                    {
                        // calculate the sma 50
                        $sma_50_sum = 0;
                        // get the sum of 50 past records
                        for ($j = 0; $j < 50; $j++) 
                        {
                            $sma_50_sum += $records[$i - $j]->close_price;
                        }
                        $sma_50 = $sma_50_sum / 50;
                        $record->sma_50 = $sma_50;

                        // calculate ema 50
                        if($i == 49)
                        {
                            $ema_50 = $sma_50;
                        } else {
                            $k = 2 / (50 + 1);
                            $ema_50 = ($k * $record->close_price) + ( (1 - $k) * $previous_record->ema_50 );
                        }
                        $record->ema_50 = $ema_50;
                    }

                    // calculate rsi 14
                    if($i >= 14)
                    {
                        $gains = $losses = [];
                        for ($j = 0; $j < 14; $j++) 
                        {
                            $change     = $records[$i-$j]->close_price - $records[$i -$j -1]->close_price;
                            $gains[]    = max($change, 0);
                            $losses[]   = max(-$change, 0);
                        }

                        $avgGain = array_sum(array_slice($gains, -14)) / 14;
                        $avgLoss = array_sum(array_slice($losses, -14)) / 14;
                        $rs = $avgGain / $avgLoss;
                        $rsi_14 = 100 - (100 / (1 + $rs));
                    
                        $record->rsi_14 = $rsi_14;
                    }


                    $record->avg_price              = $avg_price;
                    $record->price_range            = $price_change;
                    $record->percentage_change      = $percentage_change;
                    $record->previous_avg_price     = $previous_avg_price;
                    $record->previous_price_change  = $previous_price_change;

                    $record->save();
                    echo "Record saved: " . $record->open_time . "\n";
                }
            } catch (\Exception $e) {
                dd($e);
            }
        }
    }
}
