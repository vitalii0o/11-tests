<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        "id",
        "user_id"
    ];
    protected $table = 'wallets';

    public function scopeUserId($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
