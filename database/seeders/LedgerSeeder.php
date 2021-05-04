<?php

namespace Database\Seeders;

use App\Models\Ledger;
use Illuminate\Database\Seeder;

class LedgerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Ledger::create(['title' => 'SBI Current','kind' => 'BANK']);
        Ledger::create(['title' => 'HDFC Bank','kind' => 'BANK']);
        Ledger::create(['title' => 'MPOnline','kind' => 'BANK']);
        Ledger::create(['title' => 'CSC','kind' => 'BANK']);
        Ledger::create(['title' => 'Shop','kind' => 'CASH']);
        Ledger::create(['title' => 'Home','kind' => 'CASH']);
        Ledger::create(['title' => 'Customer','kind' => 'RECEIVABLES']);
        Ledger::create(['title' => 'Electricity','kind' => 'EXPENSE']);
        Ledger::create(['title' => 'Internet','kind' => 'EXPENSE']);
        Ledger::create(['title' => 'Sales','kind' => 'INCOME']);
        Ledger::create(['title' => 'Cash','kind' => 'CASH']);
        Ledger::create(['title' => 'Digipay','kind' => 'BANK']);
    }
}
