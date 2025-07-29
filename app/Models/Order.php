<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'coin_id',
        'price',
        'status',
        'quantity',
        'is_deleted',
    ];
}
