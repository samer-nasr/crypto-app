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
                    if($record->avg_price) continue;
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
