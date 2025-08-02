<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    public function coin()
    {
        return $this->belongsTo(Coin::class)->where('is_deleted', 0);
    }
}
