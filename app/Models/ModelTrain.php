<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ModelTrain extends Model
{
    protected $connection = 'mongodb'; // Use the MongoDB connection
    protected $collection = 'model_train'; // Optional: name of MongoDB collection

    protected $fillable = [
        'symbol',
        'records',
        'model_name',
        'classification_report',
        'confusion_matrix',
    ];
}
