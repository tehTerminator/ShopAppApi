<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model {

    protected $table = 'ledgers__vouchers';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cr', 
        'dr', 
        'narration', 
        'amount', 
        'user_id',
        'immutable',
        'cr_balance',
        'dr_balance',
    ];

    public function creditor() {
        return $this->belongsTo(Ledger::class, 'id', 'cr');
    }

    public function debtor() {
        return $this->belongsTo(Ledger::class, 'id', 'dr');
    }
}
