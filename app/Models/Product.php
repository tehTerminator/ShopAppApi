<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'rate'
    ];

    public function balance() {
        return $this->hasMany(Balance::class);
    }
}
