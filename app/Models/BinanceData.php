<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class BinanceData extends Model
{
    protected $collection = 'binance_data';
    protected $connection = 'mongodb';

    protected $fillable = [
        'open_time',
        'open_price',
        'high_price',
        'low_price',
        'close_price',
        'volume',
        'close_time',
        'symbol',
        'label',
        'avg_price',
        'price_range',
        'percentage_change',
        'previous_avg_price',
        'previous_price_change',
    ];
}
