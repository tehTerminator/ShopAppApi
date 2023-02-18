<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockUsageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock__usage', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_invoice_id')->nullable()->default(NULL);
            $table->foreign('sales_invoice_id')->references('id')->on('sales__invoices');
            $table->unsignedBigInteger('stock_item_id');
            $table->foreign('stock_item_id')->references('id')->on('stock__items');
            $table->double('quantity');
            $table->string('narration')->nullable()->default(NULL);
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
        Schema::dropIfExists('stock__usage');
    }
}
