<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_id',
        'item_id',
        'kind',
        'description',
        'quantity',
        'rate',
        'discount',
        'user_id'
    ];

    public function invoice() {
        return $this->hasOne(Invoice::class, 'id', 'invoice_id');
    }

    public function user() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
