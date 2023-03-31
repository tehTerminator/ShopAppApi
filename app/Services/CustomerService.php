<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Ledger;

class CustomerService {

    public static function getDefaultCustomer() {
        $customer = Customer::where('title', 'Walk-in Customer')->first();

        if (empty($customer)) {
            $ledger = Ledger::create([
                'title' => 'Walk-in Customer',
                'kind' => 'RECEIVABLE'
            ]);

            $customer = Customer::create([
                'title' => 'Walk-in Customer',
                'address' => 'Ashoknagar',
                'ledger_id' => $ledger->id
            ]);
        }

        return $customer;
    }

    public static function getCustomerLedger(int $customer_id) {
        $customer = Customer::find($customer_id);

        if(!$customer) {
            $customer = self::getDefaultCustomer();
        }

        return $customer->ledger_id;
    }

    public function __construct() {}
}