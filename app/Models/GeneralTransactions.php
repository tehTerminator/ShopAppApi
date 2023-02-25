<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralTransactions extends Model {
    protected $table = 'invoices__general_transactions';

    protected $fillable = [
        'invoice_id',
        'description',
        'quantity',
        'rate',
        'discount',
    ];

    public function invoice() {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }
}