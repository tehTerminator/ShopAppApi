<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model {

    protected $table = 'stock__items';

    protected $fillable = [
        'title', 'quantity'
    ];

    protected $hidden = [
        'imageUrl'
    ];
}