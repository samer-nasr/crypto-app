<?php

namespace App\Http\Controllers;

use App\Jobs\LabelDataJob;
use App\Models\DataLabel;
use Illuminate\Http\Request;

class DataLabelController extends Controller
{
    public function store(Request $request) {
        // dd($request->all());
        LabelDataJob::dispatch($request);

        DataLabel::create([
            'symbol' => $request->symbol,
            'threshold' => $request->threshold,
            'is_deleted' => 0
        ]);
        return redirect()->route('trains.index')->with('success', 'Data labeled successfully.');
    }
}
