<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDamageQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('damage_quotations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idDamageOrder')->nullable()->unsigned();
            $table->double('amount_pieces',10,2)->nullable()->default(0);
            $table->double('amount_paint',10,2)->nullable()->default(0);
            $table->double('amount_hand',10,2)->nullable()->default(0);
            $table->double('iva',10,2)->nullable()->default(0);
            $table->double('subtotal',10,2)->nullable()->default(0);
            $table->double('total',10,2)->nullable()->default(0);
            $table->foreign('idDamageOrder')->references('id')->on('damage_orders')->onDelete('cascade');
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
        Schema::dropIfExists('damage_quotations');
    }
}
