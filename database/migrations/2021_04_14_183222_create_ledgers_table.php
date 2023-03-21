<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLedgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ledgers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('kind', [
                'BANK',
                'WALLETS',
                'DEPOSITS',
                'CASH', 
                'PAYABLES', 
                'RECEIVABLES', 
                'EXPENSE', 
                'INCOME'
            ]);
            $table->double('balance')->default(0.00);
            $table->boolean('can_receive_payment')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ledgers');
    }
}
