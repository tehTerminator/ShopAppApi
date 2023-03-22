<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model{

    protected $table = 'invoices__stock_transactions';

    protected $fillable = [
        'invoice_id',
        'stock_item_id',
        'quantity',
    ];

    public function invoice() {
        return $this->belongsTo(Invoice::class);
    }

    public function stock() {
        return $this->hasOne(Stock::class, 'stock_item_id', 'id');
    }
}