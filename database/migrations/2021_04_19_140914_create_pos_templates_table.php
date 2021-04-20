<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pos_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('positem_id');
            $table->foreign('positem_id')->references('id')->on('pos_items');
            $table->unsignedBigInteger('item_id');
            $table->enum('kind', ['PRODUCT', 'LEDGER']);
            $table->double('rate');
            $table->double('quantity');
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
        Schema::dropIfExists('pos_templates');
    }
}
