<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClaimPicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('claim_pics', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('src_pic')->nullable()->default(null);
            $table->string('size')->nullable()->default(null);
            $table->integer('idOrder')->nullable()->unsigned();
            $table->foreign('idOrder')->references('id')->on('orders')->onDelete('cascade');
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
        Schema::dropIfExists('claim_pics');
    }
}
