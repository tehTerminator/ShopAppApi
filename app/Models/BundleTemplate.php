<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BundleTemplate extends Model {

    protected $table = 'bundles__templates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bundle_id', 'item_id', 'kind', 'rate', 'quantity'
    ];
}
