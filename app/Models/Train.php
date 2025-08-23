<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;


class Train extends Model
{
    protected $collection = 'symbols';
    protected $connection = 'mongodb';

    protected $fillable = [
        'name',
        'features',
        'is_deleted'
    ];
}
