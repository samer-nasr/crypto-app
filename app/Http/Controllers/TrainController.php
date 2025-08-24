<?php

namespace App\Http\Controllers;

use App\Models\BinanceData;
use App\Models\DataLabel;
use App\Models\Symbol;
use App\Models\Train;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TrainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trains         = Train::where('is_deleted', 0)->get();
        $symbols        = Symbol::where('is_deleted', 0)->get();
        $labeled_datas  = DataLabel::where('is_deleted', 0)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        return view('crypto.trains.index', compact('trains', 'symbols', 'labeled_datas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // create train
        $train = new Train();
        $train->name = $request->name;
        $train->features = json_encode($request->features);
        $train->is_deleted = 0;
        $train->save();

        return redirect()->route('trains.index')->with('success', 'Train created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $train = Train::find($id);
        $train->is_deleted = 1;
        $train->save();
        return redirect()->route('trains.index')->with('success', 'Train deleted successfully.');
    }

    public function backtest()
    {
        $start_balance = 100;
        $balance    = 100 ; // start with $10k
        $position   = null;  // current position
        $entryPrice = 0;
        $symbol     = 'ETHUSDT';

        $data = BinanceData::where('symbol', $symbol)
            ->where('open_time', '>=', '2025-01-08 00:00:00')
            ->where('open_time', '<=', '2025-03-08 00:00:00')
            ->get();
        // dd($data->toArray());
        foreach ($data as  $record) 
        {
            $request = new Request();
            $request['symbol'] = $symbol;
            $request['model_id'] = '68aa3e666c10daf729097f07';
            $request['date'] = Carbon::parse($record->open_time)->format('Y-m-d');

            $prediction_controller = new PredictionController();
            $prediction = $prediction_controller->predict($request);

          

            $signal         = $prediction['prediction'];
            $probability    = $prediction['probabilities'];

            //   dd($probability);

            // Simple trading logic
            if ($signal == 1 
                    && !$position 
                    // && $probability[$signal] > 0.7
                ) 
            {
                // BUY
                $position = 'long';
                $entryPrice = $record['avg_price'];
                dump('BUY ' . $record['avg_price'] . ' probability: ' . $probability[$signal] . ' buy at date: ' . $record->open_time);
            } 
            else if ($signal == -1 
                        && $position == 'long' 
                        // && $probability[$signal] > 0.7
                    )
            {
                // SELL
                $balance *= ($record['avg_price'] / $entryPrice); // update balance
                $position = null;
                dump('SELL ' . $record['avg_price'] . ' probability: ' . $probability[$signal] . ' sell at date: ' . $record->open_time);
            }
        }

        dd("final balance: " . $balance , "profit percentage: " . (($balance - $start_balance) / $start_balance) * 100);
       
    }
}
