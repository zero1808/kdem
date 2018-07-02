<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDamageOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('damage_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idOrder')->nullable()->unsigned();
            $table->integer('idDamageArea')->nullable()->unsigned();
            $table->integer('idDamage')->nullable()->unsigned();
            $table->foreign('idOrder')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('idDamageArea')->references('id')->on('damage_areas')->onDelete('cascade');
            $table->foreign('idDamage')->references('id')->on('damages')->onDelete('cascade');
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
        Schema::dropIfExists('damage_orders');
    }
}
