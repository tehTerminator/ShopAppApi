<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosItem extends Model {

    protected $table = 'pos_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'rate'
    ];

    public function pos_templates() {
        return $this->hasMany(PosTemplate::class, 'positem_id', 'id');
    }
}
