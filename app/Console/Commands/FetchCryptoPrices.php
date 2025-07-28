<?php

namespace App\Console\Commands;

use App\Http\Controllers\CryptoDataController;
use App\Models\CryptoData;
use App\Models\MongoLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchCryptoPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crypto:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch crypto prices and store them in MongoDB';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Fetch the prices of Bitcoin and Ethereum in USD
            $response = Http::get('https://api.coingecko.com/api/v3/simple/price', [
                'ids' => 'bitcoin,ethereum',
                'vs_currencies' => 'usd',
            ]);

            // Get the JSON response
            $data = $response->json();

            // Loop over the data and create a new entry in the crypto_data collection in MongoDB
            // for each coin
            foreach ($data as $symbol => $info) {
                CryptoData::create([
                    'coin' => strtoupper($symbol),
                    'price' => $info['usd'],
                    'created_att' => Carbon::now()->timezone('Asia/Beirut'),
                ]);
            }

            // Create a log entry in MongoDB for successful fetch
            MongoLog::create([
                'event' => 'fetch_success',
                'data' => $data,
                'logged_at' => now(),
            ]);
            Log::info('Fetched and saved price successfully', $data);
        } catch (\Exception $e) {
            // Log the error in the mongo_logs collection
            MongoLog::create([
                'event' => 'fetch_error',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'logged_at' => now(),
            ]);
            Log::error('Crypto fetch failed: ' . $e->getMessage());
        }
    }
}
