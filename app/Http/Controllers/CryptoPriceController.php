<?php

namespace App\Http\Controllers;

use App\Models\Coin;
use App\Models\CryptoData;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CryptoPriceController extends Controller
{
    public function index()
    {
        $mysql_coins = Coin::all();
        $coin_codes = $mysql_coins->pluck('code', 'name')->toArray();

        $coins = [];
        foreach ($mysql_coins as $c) {
            $coin = CryptoData::where('coin', strtoupper($c->name))
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], $c->code);
            $coins[$c->code] = $coin;
        }
        // dd($coins);

        // $btcPrices = CryptoData::where('coin', 'BITCOIN')
        //     ->orderBy('created_at', 'desc')
        //     ->paginate(10, ['*'], 'btc_page');

        // $ethPrices = CryptoData::where('coin', 'ETHEREUM')
        //     ->orderBy('created_at', 'desc')
        //     ->paginate(10, ['*'], 'eth_page');

        // dd($ethPrices->toArray(),$btcLabels, $btcData, $ethLabels, $ethData);

        // return view('crypto.index', compact('btcPrices', 'ethPrices'));
        return view('crypto.index', compact('coins' , 'coin_codes'));
    }

    public function getChartData(Request $request)
    {
        $btcPrices = CryptoData::where('coin', 'BITCOIN')
            ->latest()
            ->take(15)
            ->get()
            ->reverse()
            ->values();

        $ethPrices = CryptoData::where('coin', 'ETHEREUM')
            ->latest()
            ->take(15)
            ->get()
            ->reverse()
            ->values();

        return response()->json([
            'btc' => [
                'labels' => $btcPrices->pluck('created_at')->map(fn($d) => Carbon::parse($d)->setTimezone('Asia/Beirut')->format('H:i')),
                'data' => $btcPrices->pluck('price'),
            ],
            'eth' => [
                'labels' => $ethPrices->pluck('created_at')->map(fn($d) => Carbon::parse($d)->setTimezone('Asia/Beirut')->format('H:i')),
                'data' => $ethPrices->pluck('price'),
            ],
        ]);
    }
}
