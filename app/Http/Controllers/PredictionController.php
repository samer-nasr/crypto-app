<?php

namespace App\Http\Controllers;

use App\Models\BinanceData;
use App\Models\ModelTrain;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PredictionController extends Controller
{
    public function predict(Request $request)
    {
        // dd($request->symbol);
        $date       = Carbon::parse($request->date)->format('Y-m-d H:i:s') ;
        $symbol     = $request->symbol;
        $model      = ModelTrain::find($request->model_id);
        $model_path = $model->model_path;
        
        $data = BinanceData::where('symbol', $symbol)
                        ->orderBy('id', 'desc')
                        ->where('open_time', '=', $date)
                        ->get([
                            'avg_price',
                            'percentage_change',
                            'previous_avg_price',
                            'previous_price_change',
                            'price_range',
                            'ema_5',
                            'ema_10',
                            'ema_20',
                            'ema_50',
                            'sma_5',
                            'sma_10',
                            'sma_20',
                            'sma_50',
                            'rsi_14',
                        ])
                        ->first()
                        ->toArray();
        unset($data['id']);

        // dd($data);

        $endpoint = 'http://127.0.0.1:8001/predict?model_path=' . $model_path . '';

        $response = Http::post($endpoint, $data);

        // dd($response->json());

        $return = $response->json()['prediction'];
        // dd($return);

        if($return == 1) 
        {
            $return = 'Buy';
        } 
        else if($return == -1) 
        {
            $return = 'Sell';
        }
        else
        {
            $return = 'Hold';
        }

        return $return;
    }

    public function test_prediction()
    {
        $request                = new Request();
        $request['date']        = '2025-08-03 00:00:00';
        $request['symbol']      = 'ETHUSDT';
        $model_id = ModelTrain::where('symbol', $request->symbol)->where('is_deleted', 0)->latest()->first()->id;
        $request['model_id']    = $model_id;
        dd($this->predict($request));

        $symbol         = 'ETHUSDT';
        $predictions    = [];
        $data_count     = 0;
        $true_value     = 0;
        $data           = BinanceData::where('symbol', $symbol)
                        ->where('open_time', '>', '2025-07-20 00:00:00')
                        // ->where('open_time', '<', '2025-08-09 00:00:00')
                        ->get([
                            'avg_price',
                            'percentage_change',
                            'previous_avg_price',
                            'previous_price_change',
                            'price_range',
                            'open_time',
                        ])->toArray();

        $data_count     = count($data) -1;
        $data_count = 10;
        // dd($data);

        $model_path = "../model/" . $symbol . "/xgb_model_20250810_192501.pkl";
        $endpoint = 'http://127.0.0.1:8001/predict?model_path=' . $model_path . '';

        for ($i = 0 ; $i < 10 ; $i++)  
        {
            $response       = Http::post($endpoint, $data[$i]);
            // dump($data[$i]['open_time']);
            $prediction     = $response->json()['prediction'];
            $predictions[]  = $prediction;

            $currenct_price = $data[$i]['avg_price'];
            $next_price     = $data[$i + 10]['avg_price'];
            // echo 'current price: ' . $currenct_price . ' next price: ' . $next_price . ' prediction: ' . $prediction . '\n';
            // dd($currenct_price , $next_price);

            if ($prediction > 0)
            {
                if ($next_price > $currenct_price)
                {
                    $true_value++;
                }
            }
            else
            {
                if ($next_price < $currenct_price)
                {
                    $true_value++;
                }
            }
        }

        dd($predictions , ($true_value/$data_count) * 100 , $true_value);
    }


}
