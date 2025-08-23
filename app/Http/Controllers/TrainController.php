<?php

namespace App\Http\Controllers;

use App\Models\Train;
use Illuminate\Http\Request;

class TrainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trains = Train::where('is_deleted', 0)->get();
        return view('crypto.trains.index' , compact('trains'));
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
}
