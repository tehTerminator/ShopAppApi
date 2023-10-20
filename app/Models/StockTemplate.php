<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTemplate extends Model
{

    protected $table = 'stock_templates';
    protected $fillable = ['product_id', 'stock_item_id', 'quantity'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}