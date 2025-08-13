<?php

namespace App\Http\Controllers;

use App\Models\BinanceData;
use App\Models\ModelTrain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PredictionController extends Controller
{
    public function predict(Request $request)
    {
        $symbol = 'ETHUSDT';

        // $data = [
        //     'avg_price' => 114548.2875,
        //     'percentage_change' => 0.007557363439418767,
        //     'previous_avg_price' => 114240.64749999999,
        //     'previous_price_change' => -0.008042064740672344,
        //     'price_range' => 2360.8699999999953,
        // ];

        $data = BinanceData::where('symbol', $symbol)->orderBy('id', 'desc')
            ->limit(2)->get([
                'avg_price',
                'percentage_change',
                'previous_avg_price',
                'previous_price_change',
                'price_range',
            ])->toArray();
        // dd($data[1]);

        $model = ModelTrain::where('symbol', $symbol)
                        ->orderBy('created_at', 'desc')
                        ->first()
                        ->model_name;
        $model_path = "../model/" . $symbol . "/" . $model;

        $endpoint = 'http://127.0.0.1:8001/predict?model_path=' . $model_path . '';
        // dd($model_path);

        $response = Http::post($endpoint, $data[1]);

        dd($response->json() , $data[0] , $data[1]);
    }

    public function test_prediction()
    {
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
