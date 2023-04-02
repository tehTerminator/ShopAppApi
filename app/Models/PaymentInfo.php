<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentInfo extends Model {

    protected $table = 'payment_info';

    protected $fillable = [
        'invoice_id',
        'user_id',
        'voucher_id',
        'amount'
    ];

    protected $casts = [
        'invoice_id' => 'integer',
        'user_id' => 'integer',
        'voucher_id' => 'integer',
        'amount' => 'double'
    ];
}