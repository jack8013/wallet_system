<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    //
    protected $fillable = [
        'wallet_id',
        'amount',
        'type'
    ];

    public function wallet(){
        $this->belongsTo(Wallet::class);
    }
}
