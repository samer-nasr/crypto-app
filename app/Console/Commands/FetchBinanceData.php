<?php

namespace App\Console\Commands;

use App\Models\BinanceData;
use App\Models\MongoLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchBinanceData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crypto:fetch-binance-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches Binance data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $limit = 1000;
            $open_time = 0;
            $open_price = 1;
            $high_price = 2;
            $low_price = 3;
            $close_price = 4;
            $volume = 5;
            $close_time = 6;
            $startTime = 1501545600;
            $allData = [];

            do {

                // Fetch the prices of Bitcoin and Ethereum in USD
                $response = Http::get(env('BINANCE_ENDPOINT_PRICE_API_BTC'), [
                    'symbol' => 'BTCUSDT',
                    'interval' => '1d',
                    'limit' => 1000,
                    'startTime' => $startTime
                ]);

                $data = $response->json();

                if (empty($data)) break;

                $allData = array_merge($allData, $data);

                // Set next start time to last close time + 1 ms
                $startecho = $data[0][0];
                $lastCloseTime = $data[count($data) - 1][6];
                $startTime = $lastCloseTime + 1;

                // Sleep if needed to respect API limits
                usleep(1000000); // 0.5 sec pause to avoid hitting rate limits
                echo "fetched from " . Carbon::createFromTimestamp($startecho / 1000)->toDateTimeString() . "to " . Carbon::createFromTimestamp($lastCloseTime / 1000)->toDateTimeString() ." count data " . count($allData). "\n";
            } while (count($data) === $limit);
            echo "done fetching lets insert data in mongo count data " . count($allData) . "\n";


            foreach ($allData as $info) {
                $open_time = $info[0];
                $open_price = $info[1];
                $high_price = $info[2];
                $low_price = $info[3];
                $close_price = $info[4];
                $volume = $info[5];
                $close_time = $info[6];
                BinanceData::create([
                    'open_time' =>  Carbon::createFromTimestamp($open_time / 1000)->toDateTimeString(),
                    'open_price' => $open_price,
                    'high_price' => $high_price,
                    'low_price' => $low_price,
                    'close_price' => $close_price,
                    'volume' => $volume,
                    'close_time' => Carbon::createFromTimestamp($close_time / 1000)->toDateTimeString(),
                ]);
            }

            // Create a log entry in MongoDB for successful fetch
            MongoLog::create([
                'event' => 'fetch_binance_success',
                'data' => $allData,
                'logged_at' => now(),
            ]);
            Log::info('Fetched and saved price successfully', $allData);
        } catch (\Exception $e) {
            // Log the error in the mongo_logs collection
            MongoLog::create([
                'event' => 'fetch_error_binance',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'logged_at' => now(),
            ]);
            Log::error('Crypto fetch failed: ' . $e->getMessage());
        }
    }
}
