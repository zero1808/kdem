<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSatBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sat_bills', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idOrder')->nullable()->unsigned();
            $table->date('billing_date')->nullable()->default(null);
            $table->string('folio')->nullable()->default(null);
            $table->integer('idDealer')->nullable()->unsigned();
            $table->double('total_amount')->nullable()->default(null);
            $table->integer('idStatusBill')->nullable()->unsigned();
            $table->date('pay_date')->nullable()->default(null);
            $table->string('bank')->nullable()->default(null);
            $table->double('import_mxn')->nullable()->default(null);
            $table->foreign('idOrder')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('idDealer')->references('id')->on('dealers')->onDelete('set null');
            $table->foreign('idStatusBill')->references('id')->on('status_bills')->onDelete('set null');
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
        Schema::dropIfExists('sat_bills');
    }
}
