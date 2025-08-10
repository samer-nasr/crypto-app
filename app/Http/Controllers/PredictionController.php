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

        $data = [
            'avg_price' => 114548.2875,
            'percentage_change' => 0.007557363439418767,
            'previous_avg_price' => 114240.64749999999,
            'previous_price_change' => -0.008042064740672344,
            'price_range' => 2360.8699999999953,
        ];

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
}
