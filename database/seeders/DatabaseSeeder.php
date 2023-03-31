<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            LedgerSeeder::class,
            ProductSeeder::class
        ]);

        Customer::create(['title' => 'Walk-in Customer','address' => 'Ashoknagar', 'ledger_id' => 1]);


        DB::unprepared('DROP FUNCTION IF EXISTS calcTransactionAmount');
        DB::unprepared('
                CREATE FUNCTION calcTransactionAmount(
                    quantity INT, 
                    rate INT, 
                    discount INT) RETURNS INT
                    RETURN (quantity * rate) * (1 - discount / 100)
        ');
    }
}
