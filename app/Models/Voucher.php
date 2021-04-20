<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cr', 'dr', 'narration', 'amount', 'user_id'
    ];

    public function creditor() {
        return $this->hasOne(Ledger::class, 'id', 'cr');
    }

    public function debtor() {
        return $this->hasOne(Ledger::class, 'id', 'dr');
    }
}
