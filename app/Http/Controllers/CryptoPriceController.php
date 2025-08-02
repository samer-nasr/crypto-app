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
        $mysql_coins = Coin::where('code', '!=', 'USD')->get();

        $coin_codes = $mysql_coins->pluck('code', 'name')->toArray();

        $coins = [];
        foreach ($mysql_coins as $c) {
            $coin = CryptoData::where('coin', strtoupper($c->name))
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], $c->code);
            $coins[$c->code] = $coin;
        }

        return view('crypto.index', compact('coins' , 'coin_codes'));
    }

    public function getChartData(Request $request)
    {
        $mysql_coins = Coin::where('code', '!=', 'USD')->get();
        
        $coins = [];

        foreach($mysql_coins as $c) {
             $coin = CryptoData::where('coin', strtoupper($c->name))
            ->latest()
            ->take(15)
            ->get()
            ->reverse()
            ->values();

            $coins[$c->code] = $coin;
        }

        $response_array = [];
        foreach($coins as $k => $v) {
            $response_array[strtolower($k)] = [
                'labels' => $v->pluck('created_at')->map(fn($d) => Carbon::parse($d)->setTimezone('Asia/Beirut')->format('H:i')),
                'data' => $v->pluck('price'),
            ];
        }

        return response()->json($response_array);
    }
}
