<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailedTransactions extends Model {

    protected $table = 'invoices__detailed_transactions';

    protected $fillable = [
        'invoice_id',
        'item_id',
        'user_id',
        'kind',
        'description',
        'quantity',
        'rate',
        'discount',
    ];

    public function invoice() {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }
}