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
}