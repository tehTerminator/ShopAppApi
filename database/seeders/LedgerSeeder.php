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
        Ledger::create(['title' => 'Walk-in Customer','kind' => 'RECEIVABLE']);
        Ledger::create(['title' => 'SBI Current','kind' => 'BANK']);
        Ledger::create(['title' => 'HDFC Bank','kind' => 'BANK']);
        Ledger::create(['title' => 'MPOnline','kind' => 'WALLET']);
        Ledger::create(['title' => 'CSC','kind' => 'WALLET']);
        Ledger::create(['title' => 'Shop','kind' => 'CASH']);
        Ledger::create(['title' => 'Home','kind' => 'CASH']);
        Ledger::create(['title' => 'Electricity','kind' => 'EXPENSE']);
        Ledger::create(['title' => 'Internet','kind' => 'EXPENSE']);
        Ledger::create(['title' => 'Sales','kind' => 'INCOME']);
        Ledger::create(['title' => 'Cash','kind' => 'CASH']);
        Ledger::create(['title' => 'Digipay','kind' => 'WALLET']);
    }
}
