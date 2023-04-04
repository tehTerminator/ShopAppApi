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
        'cr', 'dr', 'narration', 'amount', 'user_id', 'immutable'
    ];

    protected $hidden = [
        'immutable'
    ];

    protected $casts = [
        'cr' => 'integer',
        'dr' => 'integer',
        'amount' => 'double',
        'user_id' => 'integer'
    ];

    public function creditor() {
        return $this->hasOne(Ledger::class, 'id', 'cr');
    }

    public function debtor() {
        return $this->hasOne(Ledger::class, 'id', 'dr');
    }
}
