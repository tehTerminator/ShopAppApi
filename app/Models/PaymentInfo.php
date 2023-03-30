<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentInfo extends Model {

    protected $table = 'invoices__payment_info';

    protected $fillable = [
        'invoice_id',
        'contact_id',
        'voucher_id',
        'amount'
    ];


    public function invoice(){
        return $this->belongsTo(Invoice::class);
    }

    public function contact() {
        return $this->hasOne(Contact::class);
    }
    
    public function voucher() {
        return $this->hasOne(Voucher::class);
    }
}