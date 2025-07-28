<?php

namespace App\Http\Controllers;

use App\Models\CryptoData;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CryptoPriceController extends Controller
{
    public function index()
    {

        $btcPrices = CryptoData::where('coin', 'BITCOIN')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'btc_page');

        $ethPrices = CryptoData::where('coin', 'ETHEREUM')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'eth_page');

        $btcLabels = $btcPrices->sortBy('created_at')->pluck('created_at')->map(fn($d) => \Carbon\Carbon::parse($d)->format('H:i'));
        $btcData = $btcPrices->sortBy('created_at')->pluck('price');

        $ethLabels = $ethPrices->sortBy('created_at')->pluck('created_at')->map(fn($d) => \Carbon\Carbon::parse($d)->format('H:i'));
        $ethData = $ethPrices->sortBy('created_at')->pluck('price');
        // dd($ethPrices->toArray(),$btcLabels, $btcData, $ethLabels, $ethData);

        return view('crypto.index', compact('btcPrices', 'ethPrices'));
    }

    public function getChartData(Request $request)
    {
        $btcPrices = CryptoData::where('coin', 'BITCOIN')
            ->latest()
            ->take(20)
            ->get()
            ->reverse()
            ->values();

        $ethPrices = CryptoData::where('coin', 'ETHEREUM')
            ->latest()
            ->take(20)
            ->get()
            ->reverse()
            ->values();

        return response()->json([
            'btc' => [
                'labels' => $btcPrices->pluck('created_at')->map(fn($d) => Carbon::parse($d)->format('H:i')),
                'data' => $btcPrices->pluck('price'),
            ],
            'eth' => [
                'labels' => $ethPrices->pluck('created_at')->map(fn($d) => Carbon::parse($d)->format('H:i')),
                'data' => $ethPrices->pluck('price'),
            ],
        ]);
    }
}
