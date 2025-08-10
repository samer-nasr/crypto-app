<?php

namespace App\Http\Controllers;

use App\Models\BinanceData;
use App\Models\ModelTrain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ModelTrainController extends Controller
{
    public function train(Request $request)
    {
        $symbols = ['BTCUSDT', 'ETHUSDT'];

        foreach ($symbols as $symbol) 
        {
            $records = BinanceData::where('symbol', $symbol)
                ->whereNotNull('percentage_change')
                ->whereNotNull('label')
                ->get([
                    'avg_price',
                    'percentage_change',
                    'previous_avg_price',
                    'previous_price_change',
                    'price_range',
                    'label'
                ])->toArray();

            $endpoint = 'http://127.0.0.1:8001/train?symbol=' . $symbol . '';
            $response = Http::post($endpoint, [
                "records" => $records,
            ]);

            $response = $response->json();

            // save training in mongo
            $modelTrain                         = new ModelTrain();

            $modelTrain->symbol                 = $symbol;
            $modelTrain->records                = $response["records_used"];
            $modelTrain->model_name             = $response["model_name"];
            $modelTrain->classification_report  = $response["classification_report"];
            $modelTrain->confusion_matrix       = $response["confusion_matrix"];

            $modelTrain->save();

            // dd($modelTrain);
        }
        dd("done");
    }
}
