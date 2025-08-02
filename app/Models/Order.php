<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'coin_id',
        'counter_coin_id',
        'price',
        'counter_price',
        'status',
        'type',
        'quantity',
        'is_deleted',
    ];

    public function coin()
    {
        return $this->belongsTo(Coin::class)->where('is_deleted', 0);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
