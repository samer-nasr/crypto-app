<?php

namespace App\Http\Controllers;

use App\Models\Coin;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $coins = Coin::where('code', '!=', 'USD')->get();

        return view('crypto.orders.index' , compact('coins'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $order = Order::create([
            'user_id' => Auth::id(),
            'coin_id' => $request->coin,
            'type' => $request->type,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'counter_price' => $request->counter_price,
            'is_deleted' => 0,
            'status' => 'pending'
        ]);

        return redirect()->route('orders.index')->with('success', 'Order created successfully.');
    }
}
