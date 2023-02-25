<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model {

    protected $table = 'sales__invoices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'contact_id', 'paid', 'amount', 'user_id', 'kind'
    ];

    public function contact() {
        return $this->belongsTo(Contact::class, 'id', 'contacts');
    }

    public function generalTransactions() {
        return $this->hasMany(GeneralTransactions::class, 'invoice_id', 'id');
    }

    public function detailedTransactions() {
        return $this->hasMany(DetailedTransactions::class, 'invoice_id', 'id');
    }
}
