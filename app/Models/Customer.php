<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'address', 'mobile', 'ledger_id'
    ];

    public function invoices() {
        return $this->hasMany(Invoice::class);
    }

    public function ledger() {
        return $this->hasOne(Ledger::class);
    }

    protected $casts = [
        'ledger_id' => 'integer'
    ];
}
