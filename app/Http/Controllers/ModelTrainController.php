<?php

namespace App\Http\Controllers;

use App\Models\BinanceData;
use App\Models\ModelTrain;
use App\Models\Symbol;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ModelTrainController extends Controller
{
    public function train(Request $request)
    {
        $is_test    = $request->has('is_test') ? 1 : 0;
        $symbol     = $request->symbol;
        $label_time = $request->label_time;
        $label      = 'label_' . $label_time;
        $last_date  = $request->last_date ? Carbon::parse($request->last_date)->format('Y-m-d H:i:s') : null;
        // dd($last_date);

        $query = BinanceData::where('symbol', $symbol)
            ->whereNotNull('percentage_change')
            ->whereNotNull('ema_50')
            // ->whereNotNull('label')
            ->whereNotNull($label);

        if ($last_date) {
            $query = $query->where('open_time', '<=', $last_date);
        }

        $query = $query->select(
            'avg_price',
            'percentage_change',
            'previous_avg_price',
            'previous_price_change',
            'price_range',
            'ema_20',
            'ema_5',
            'ema_10',
            'ema_50',
            'sma_5',
            'sma_10',
            'sma_20',
            'sma_50',
            'rsi_14',
            $label,
            'open_time'
        );

        $records = $query->get(
            // [
            //     'avg_price',
            //     'percentage_change',
            //     'previous_avg_price',
            //     'previous_price_change',
            //     'price_range',
            //      $label,
            //     'open_time'
            // ]
        )->map(function ($item) use ($label) {
            $item->label = $item[$label] ?? null;
            unset($item[$label]);
            return $item;
        })->toArray();

        // dd($records);
        $last_record_time = $query->orderBy('open_time', 'desc')
            ->first()
            ->open_time;

        $records = array_map(function ($record) {
            unset($record['id']);
            unset($record['open_time']);
            return $record;
        }, $records);

        // dd($last_record_time,$records);

        $endpoint = 'http://127.0.0.1:8001/train?symbol=' . $symbol . '&test=' . $is_test . '';
        // dd($endpoint);
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
        $modelTrain->is_test                = $is_test;

        $modelTrain->save();

        return $modelTrain;
    }

    public function index(Request $request)
    {
        $models = ModelTrain::where('is_deleted', 0)->get();
        $symbols = Symbol::where('is_deleted', 0)->get();

        $label_times = config('constants.label_time');

        // dd($label_times);
        return view('crypto.models.index', compact('models', 'symbols', 'label_times'));
    }

    public function store(Request $request)
    {
        
        $response = $this->train($request);

        return redirect()->route('models.index')->with('success', 'Model created successfully.');
    }

    public function destroy($id)
    {

        // $model = ModelTrain::where('id', $id)->first();
        $model = ModelTrain::find($id);

        $model->is_deleted = 1;
        $model->save();

        // delete model file

        $model_path = config('constants.models_path') . $model->symbol . '/' . $model->model_name;
        $model_path = base_path($model_path);

        if (file_exists($model_path)) {
            unlink($model_path);
        }

        return redirect()->route('models.index')->with('success', 'Model deleted successfully.');
    }
}
