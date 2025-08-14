<?php

namespace App\Jobs;

use App\Models\BinanceData;
use App\Models\Symbol;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class LabelDataJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $symbols = Symbol::where('is_deleted', 0)->get()->pluck('symbol')->toArray();
            $labels = config('constants.label_time');

            $threshold = 0.0050; // 0.5%
            $lookAheadDays = 10;

            foreach ($symbols as $symbol) {
                foreach ($labels as $index => $label_count) {
                    $label = 'label_'.$label_count;
                    $symbol_prices = BinanceData::where('symbol', $symbol)->get();

                    for ($i = 0; $i < $symbol_prices->count(); $i++) {
                        // reset all labels
                        // $symbol_prices[$i]->label = Null;
                        // $symbol_prices[$i]->save();
                        // echo $symbol_prices[$i]->label . ' ' . $i . '\n';
                        // continue;

                        // label against next 10 days
                        $symbol_record = $symbol_prices[$i];
                        $symbol_price = $symbol_record->avg_price;
                        if ($i + $label_count >= $symbol_prices->count()) continue;
                        // get the next 10 days price
                        $nextDaysPrice = $symbol_prices[$i + $label_count]->avg_price;

                        $change = ($nextDaysPrice - $symbol_price) / $symbol_price;

                        if ($change >= $threshold) {
                            $symbol_record->{$label} = 1;
                        } else if ($change <= -$threshold) {
                            $symbol_record->{$label} = -1;
                        } else {
                            $symbol_record->{$label} = 0;
                        }

                        echo $symbol_record->{$label} . ' '  . "\n";
                        $symbol_record->save();
                        // dump($next10DaysPrice);
                    }
                }
            }
        } 
        catch (\Exception $e) 
        {
            dd($e->getMessage());
        }
    }
}
