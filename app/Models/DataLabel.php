<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class DataLabel extends Model
{
    protected $collection = 'datalabels';
    protected $connection = 'mongodb';

    protected $fillable = [
        'symbol',
        'threshold',
        'is_deleted'
    ];
}
