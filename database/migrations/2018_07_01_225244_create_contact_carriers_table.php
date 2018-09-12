<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactCarriersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_carriers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('idUser')->nullable()->unsigned();
            $table->integer('idCarrier')->nullable()->unsigned();
            $table->integer('status')->default(1);
            $table->foreign('idCarrier')->references('id')->on('carriers')->onDelete('cascade');
            $table->foreign('idUser')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('contact_carriers');
    }
}
