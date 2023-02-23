<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bundle extends Model {


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'rate'
    ];

    public function bundle_template() {
        return $this->hasMany(BundleTemplate::class, 'bundle_id', 'id');
    }
}
