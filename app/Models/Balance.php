<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model {

    protected $table = 'balance';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ledger_id', 'opening', 'closing'
    ];

    public function ledger() {
        return $this->hasOne(Ledger::class);
    }
}
