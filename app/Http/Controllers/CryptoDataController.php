<?php

namespace App\Http\Controllers;

use App\Models\BinanceData;
use App\Models\CryptoData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class CryptoDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function update_crypto_data()
    {
        $last_crypto_data = CryptoData::orderBy('created_at', 'desc')->first();
        $symbols = ['BTCUSDT', 'ETHUSDT'];

        foreach ($symbols as $symbol) 
        {
            $binance_data = BinanceData::where('symbol', $symbol)
                                        ->orderBy('open_time', 'asc')
                                        ->whereNotNull('avg_price')
                                        ->get();
            foreach ($binance_data as $data) {
                CryptoData::create([
                    'coin' => $symbol == 'BTCUSDT' ? 'BITCOIN' : 'ETHEREUM',
                    'price' => $data->avg_price,
                    'open_time' => $data->open_time
                ]);
            }
        }
      
        dd("done");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request = NULL)
    {
        dd(Carbon::now()->timezone('Asia/Beirut'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        dd('here');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
