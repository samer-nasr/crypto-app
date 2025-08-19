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
        'label_1',
        'label_2',
        'label_3',
        'label_4',
        'label_5',
        'label_6',
        'label_7',
        'label_8',
        'label_9',
        'label_10',
        'avg_price',
        'price_range',
        'percentage_change',
        'previous_avg_price',
        'previous_price_change',
        'sma_5',
        'sma_10',
        'sma_20',
        'sma_50',
        'ema_5',
        'ema_10',
        'ema_20',
        'ema_50',
        'rsi_14',
        'note'
    ];
}
