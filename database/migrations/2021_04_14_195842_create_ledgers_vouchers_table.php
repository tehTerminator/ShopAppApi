<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLedgersVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ledgers__vouchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cr');
            $table->unsignedBigInteger('dr');
            $table->foreign('cr')->on('ledgers')->references('id');
            $table->foreign('dr')->on('ledgers')->references('id');
            $table->text('narration');
            $table->double('amount')->default(0.00);
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->on('users')->references('id');
            $table->boolean('immutable')->default(false);
            $table->softDeletes();
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
        Schema::dropIfExists('ledgers__vouchers');
    }
}
