<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'paymentMethod', 'paid', 'amount', 'user_id'
    ];

    public function customer() {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function transactions() {
        return $this->hasMany(Transaction::class, 'invoice_id', 'id');
    }
}
