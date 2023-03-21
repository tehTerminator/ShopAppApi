<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BalanceSnapshot extends Model {

    protected $table = 'ledgers__balance_shapshots';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ledger_id', 'balance'
    ];

    public function ledger() {
        return $this->belongsTo(Ledger::class);
    }
}
