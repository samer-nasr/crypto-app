<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class MongoLog extends Model
{
    protected $connection = 'mongodb'; // Use the MongoDB connection
    protected $collection = 'logs'; // Optional: name of MongoDB collection

    protected $fillable = [
        'event', 'data', 'message', 'trace','new_account','old_account','order_type', 'logged_at'
    ];
}
