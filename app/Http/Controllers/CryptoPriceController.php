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
                ->orderBy('open_time', 'desc')
                ->paginate(15, ['*'], $c->code);
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
            // ->latest()
            ->orderBy('open_time', 'desc')
            ->take(15)
            ->get()
            ->reverse()
            ->values();
            // dd($coin->toArray());

            $coins[$c->code] = $coin;
        }

        $response_array = [];
        foreach($coins as $k => $v) {
            $response_array[strtolower($k)] = [
                // 'labels' => $v->pluck('created_at')->map(fn($d) => Carbon::parse($d)->setTimezone('Asia/Beirut')->format('m-d')),
                'labels' => $v->pluck('open_time')->map(fn($d) => Carbon::parse($d)->format('m-d')),
                'data' => $v->pluck('price'),
            ];
        }

        return response()->json($response_array);
    }

    public function test()
    {
        $mongo_data_btc = CryptoData::where('coin', 'BITCOIN')
                                    ->where('created_at', '>=', Carbon::now()->subDays(1))
                                    ->get();
        $first_date = '';
        $i = 0;

        foreach ($mongo_data_btc as $d) {
            if($i == 0) dump($d->created_at->format('Y-m-d H:i'));
            $i++;
            if($i-1  == 4) $i =0;
        }

        dd('stop');

        foreach ($mongo_data_btc as $d) {
            // dump($d->created_at->format('Y-m-d H:i'));
            if(empty($first_date)) {
                $first_date = $d->created_at;
                dump($d->created_at->format('Y-m-d H:i').' first => '. $d->price);
                $i++;
                continue;
            }
            if(Carbon::parse($d->created_at)->format('Y-m-d H:i') == Carbon::parse($first_date)->addMinutes(5)->format('Y-m-d H:i') || $i == 5) {
                // dd('here');
                dump($d->created_at.' => '. $d->price.' '.$i);
                $first_date = $d->created_at;
                // dump('ttt: '.$first_date->format('Y-m-d H:i'));
                $i++;
                if($i-1  == 5) $i =0;
            }
        }
        dd('stop');
        $mongo_data_eth = CryptoData::where('coin', 'ETHEREUM')->get();
        dd($mongo_data_btc->toArray() , $mongo_data_eth->toArray());
    }
}
