<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosTemplate extends Model {

    protected $table = 'pos_templates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'positem_id', 'item_id', 'kind', 'rate', 'quantity'
    ];

    protected $casts = [
        'positem_id' => 'integer',
        'item_id' => 'integer',
        'rate' => 'double',
        'quantity' => 'double'
    ];
}
