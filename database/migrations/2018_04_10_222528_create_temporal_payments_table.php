<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemporalPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temporal_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idOrder')->nullable()->unsigned();
            $table->double('amount_gmx')->nullable()->default(0);
            $table->date('pay_date_gmx')->nullable()->default(null);
            $table->string('smx_claim_number')->nullable()->default(null);
            $table->double('amount_smx')->nullable()->default(0);
            $table->date('pay_date_smx')->nullable()->default(null);
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
        Schema::dropIfExists('temporal_payments');
    }
}
