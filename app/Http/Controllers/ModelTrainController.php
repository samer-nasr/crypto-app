<?php

namespace App\Http\Controllers;

use App\Models\BinanceData;
use App\Models\ModelTrain;
use App\Models\Symbol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ModelTrainController extends Controller
{
    public function train(Request $request)
    {
        $symbol     = $request->symbol;
        $label_time = $request->label_time;
        $last_date  = $request->last_date;

        $query = BinanceData::where('symbol', $symbol)
            ->whereNotNull('percentage_change')
            ->whereNotNull('label');
            // ->where('label_time' , $label_time)

        if ($last_date) {
            $query = $query->where('open_time', '<=', $last_date);
        }

        $records = $query->get([
                    'avg_price',
                    'percentage_change',
                    'previous_avg_price',
                    'previous_price_change',
                    'price_range',
                    'label',
                    'open_time'
                ])->toArray();

        // dd($query);
        $last_record_time = $query->orderBy('open_time', 'desc')
                                ->first()
                                ->open_time;

        $records = array_map(function ($record) {
            unset($record['id']);
            unset($record['open_time']);
            return $record;
        }, $records);

        // dd($last_record_time,$records);

        $endpoint = 'http://127.0.0.1:8001/train?symbol=' . $symbol . '';
        $response = Http::post($endpoint, [
            "records" => $records,
        ]);

        $response = $response->json();

        // dd($response);

        // save training in mongo
        $modelTrain                         = new ModelTrain();

        $modelTrain->symbol                 = $symbol;
        $modelTrain->records                = $response["records_used"];
        $modelTrain->last_record_time       = $last_record_time;
        $modelTrain->label_time             = $label_time;
        $modelTrain->model_path             = '../model/' . $symbol . '/' . $response["model_name"];
        $modelTrain->is_deleted             = 0;
        $modelTrain->model_name             = $response["model_name"];
        $modelTrain->classification_report  = $response["classification_report"];
        $modelTrain->confusion_matrix       = $response["confusion_matrix"];

        $modelTrain->save();

        return $modelTrain;

        


        // $symbols = ['BTCUSDT', 'ETHUSDT'];

        // foreach ($symbols as $symbol) 
        // {
        //     $records = BinanceData::where('symbol', $symbol)
        //         // ->where('open_time', '<=', '2025-07-30 00:00:00')
        //         ->whereNotNull('percentage_change')
        //         ->whereNotNull('label')
        //         // ->orderBy('open_time', 'desc')
        //         ->get([
        //             'avg_price',
        //             'percentage_change',
        //             'previous_avg_price',
        //             'previous_price_change',
        //             'price_range',
        //             'label',
        //             // 'open_time',
        //         ])->toArray();
        //     // dd($records);

        //     $last_record_time = $records[count($records) - 1]['open_time'];

        //     $endpoint = 'http://127.0.0.1:8001/train?symbol=' . $symbol . '';
        //     $response = Http::post($endpoint, [
        //         "records" => $records,
        //     ]);

        //     $response = $response->json();

        //     // save training in mongo
        //     $modelTrain                         = new ModelTrain();

        //     $modelTrain->symbol                 = $symbol;
        //     $modelTrain->records                = $response["records_used"];
        //     $modelTrain->last_record_time       = $last_record_time;
        //     $modelTrain->model_name             = $response["model_name"];
        //     $modelTrain->classification_report  = $response["classification_report"];
        //     $modelTrain->confusion_matrix       = $response["confusion_matrix"];

        //     $modelTrain->save();

        //     // dd($modelTrain);
        // }
    }

    public function index(Request $request)
    {
        $models = ModelTrain::all();
        $symbols = Symbol::where('is_deleted', 0)->get();

        $label_times = config('constants.label_time');

        // dd($label_times);
        return view('crypto.models.index', compact('models', 'symbols', 'label_times'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $response = $this->train($request);

        dd($response);
    }
}
