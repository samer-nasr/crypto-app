<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Symbol extends Model
{
    protected $collection = 'symbols';
    protected $connection = 'mongodb';

    protected $fillable = [
        'symbol',
        'code',
        'is_deleted'
    ];
}
