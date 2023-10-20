<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockUsage extends Model
{
    protected $table = 'stock_usage';
    protected $fillable = ['stock_item_id', 'user_id', 'quantity', 'narration', 'balance'];

    public function stockItem()
    {
        return $this->belongsTo(Stock::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}