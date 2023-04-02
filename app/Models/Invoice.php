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
        'customer_id', 'paid', 'amount', 'user_id'
    ];

    protected $casts = [
        'customer_id' => 'integer',
        'paid' => 'boolean',
        'amount' => 'double',
        'user_id' => 'integer'
    ];

    public function customer() {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function transactions() {
        return $this->hasMany(Transaction::class, 'invoice_id', 'id');
    }

    public function paymentInfo() {
        return $this->hasMany(PaymentInfo::class);
    }
}
