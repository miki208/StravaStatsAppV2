<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncingUser extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    protected $primaryKey = 'user_id';
    public $incrementing = false;
}
