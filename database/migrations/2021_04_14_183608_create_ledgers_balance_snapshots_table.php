<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLedgersBalanceSnapshotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ledgers__balance_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ledger_id');
            $table->foreign('ledger_id')->references('id')->on('ledgers');
            $table->double('balance');
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
        Schema::dropIfExists('ledgers__balance_snapshots');
    }
}
