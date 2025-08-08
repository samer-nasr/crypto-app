<?php

namespace App\Console\Commands;

use App\Models\BinanceData;
use App\Models\CryptoData;
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
        $limit = 1000;
        $symbols = [ 'ETHUSDT', 'BTCUSDT'];
        $endTime = Carbon::now()->subDays(1)->startOfDay()->timestamp * 1000;
        $startTime = 1501545600;
        $allData = [];
        try {
            
            foreach ($symbols as $symbol) {
                // Get the start time from mongo last record
                $last_time = BinanceData::where('symbol', $symbol)->orderBy('open_time', 'desc')->first();
                
                if ($last_time) {
                    $startTime = Carbon::parse($last_time->open_time)->addDays(1)->timestamp*1000;
                }
                echo "start from " . Carbon::createFromTimestamp($startTime)->toDateTimeString() . "\n";
                do {
                    // Fetch the prices of Bitcoin and Ethereum in USD
                    $response = Http::get(env('BINANCE_ENDPOINT_PRICE_API'), [
                        'symbol' => $symbol,
                        'interval' => '1d',
                        'limit' => 1000,
                        'startTime' => $startTime,
                        'endTime' => $endTime
                    ]);


                    $data = $response->json();
                    // dd($data);

                   
                    $data = array_map(function ($d) use ($symbol) {
                        return [
                            'open_time' => $d[0],
                            'open_price' => $d[1],
                            'high_price' => $d[2],
                            'low_price' => $d[3],
                            'close_price' => $d[4],
                            'volume' => $d[5],
                            'close_time' => $d[6],
                            'symbol' => $symbol,
                        ];
                    }, $data);

                    if (empty($data)) break;

                    $allData = array_merge($allData, $data);

                    // Set next start time to last close time + 1 ms
                    $startecho = $data[0]['open_time'];
                    $lastCloseTime = $data[count($data) - 1]['close_time'];
                    $startTime = $lastCloseTime + 1;
                    echo count($data) . "\n";

                    // Sleep if needed to respect API limits
                    usleep(1000000); // 0.5 sec pause to avoid hitting rate limits
                    echo "fetched from " . Carbon::createFromTimestamp($startecho / 1000)->toDateTimeString() . "to " . Carbon::createFromTimestamp($lastCloseTime / 1000)->toDateTimeString() . " count data " . count($allData) . "\n";
                } while (count($data) === $limit);
            }
            echo "done fetching lets insert data in mongo count data " . count($allData) . "\n";

            // dd($allData);
            foreach ($allData as $info) {
                BinanceData::create([
                    'open_time' =>  Carbon::createFromTimestamp($info['open_time'] / 1000)->toDateTimeString(),
                    'open_price' => $info['open_price'],
                    'high_price' => $info['high_price'],
                    'low_price' => $info['low_price'],
                    'close_price' => $info['close_price'],
                    'volume' => $info['volume'],
                    'close_time' => Carbon::createFromTimestamp($info['close_time'] / 1000)->toDateTimeString(),
                    'symbol' => $info['symbol']
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
