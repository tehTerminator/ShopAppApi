<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model {

    protected $table = 'sales__invoices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'paid', 'amount', 'user_id'
    ];

    public function customer() {
        return $this->belongsTo(Contact::class, 'id', 'customer_id');
    }

    public function transactions() {
        return $this->hasMany(Transaction::class, 'invoice_id', 'id');
    }
}
