<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'address', 'mobile'
    ];

    public function sales_invoices() {
        return $this->hasMany(SalesInvoices::class);
    }
}
