<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function IsSynced() : bool
    {
        return !$this->hasOne(SyncingUser::class, 'user_id', 'user_id')->exists();
    }

    protected $primaryKey = 'user_id';
    public $incrementing = false;
}
