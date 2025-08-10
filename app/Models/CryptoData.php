<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class CryptoData extends Model
{
    protected $connection = 'mongodb'; // Use the MongoDB connection
    protected $collection = 'crypto_data'; // Optional: name of MongoDB collection

    protected $fillable = [
        'coin', 'price', 'label','open_time'
    ];
}