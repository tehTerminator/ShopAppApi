<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model {

    protected $table = 'stock_items';

    protected $fillable = [
        'title',
        'quantity',
        'imageUrl'
    ];

    protected $hidden = [
        'imageUrl' // Not Implemented Yet
    ];

    /**
     * Increases the Quantity by $amount
     */
    function increaseQuantity($amount) {
        $this->quantity += $amount;
        $this->save();
    }

    /**
     * Decreases the Quantity by $amount
     */
    function decreaseQuantity($amount) {
        $this->quantity -= $amount;
        $this->save();
    }
}