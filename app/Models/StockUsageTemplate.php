<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockUsageTemplate extends Model {

    protected $table = 'stock__usage_template';

    protected $fillable = [
        'stock_item_id',
        'product_id',
        'quantity'
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function stock() {
        return $this->belongsTo(Stock::class, 'stock_item_id', 'id');
    }
}