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
        'model_path',
        'classification_report',
        'confusion_matrix',
        'last_record_time',
        'label_days',
        'is_deleted',
        'is_test',
        'train_id'
    ];
}
