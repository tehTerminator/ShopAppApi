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
}