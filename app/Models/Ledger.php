<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ledger extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'kind', 'balance', 'can_receive_payment'
    ];

    public function balance_snapshot() {
        return $this->hasMany(Balance::class);
    }
}
