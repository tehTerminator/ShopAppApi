<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ledger extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'kind'
    ];

    public function balance() {
        return $this->hasMany(Balance::class);
    }
}
