<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('vin');
            $table->integer('idModelo')->nullable()->unsigned();
            $table->integer('idDealer')->nullable()->unsigned();
            $table->date('arrive_date')->nullable();
            $table->date('report_date')->nullable();
            $table->integer('idStatus')->nullable()->unsigned();
            $table->integer('idReasonReject')->nullable()->unsigned();
            $table->integer('idCarrier')->nullable()->unsigned();
            $table->string('smx_gmx')->nullable()->default(null);
            $table->double('total_amount')->nullable()->default(null);
            $table->integer('status')->default(1);
            $table->foreign('idModelo')->references('id')->on('car_models')->onDelete('set null');
            $table->foreign('idDealer')->references('id')->on('dealers')->onDelete('set null');
            $table->foreign('idStatus')->references('id')->on('status_orders')->onDelete('set null');
            $table->foreign('idReasonReject')->references('id')->on('reason_rejects')->onDelete('set null');
            $table->foreign('idCarrier')->references('id')->on('carriers')->onDelete('set null');
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
        Schema::dropIfExists('orders');
    }
}
