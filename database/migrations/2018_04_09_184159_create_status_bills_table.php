<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatusBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_bills', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
        
        DB::table('status_bills')->insert(
        array(
            'name' => 'accepted',
        )
        );
        
        DB::table('status_bills')->insert(
        array(
            'name' => 'in_process',
        )
        );
        
        DB::table('status_bills')->insert(
        array(
            'name' => 'rejected',
        )
        );
        
        DB::table('status_bills')->insert(
        array(
            'name' => 'canceled',
        )
        );
        
        DB::table('status_bills')->insert(
        array(
            'name' => 'valid',
        )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('status_bills');
    }
}
