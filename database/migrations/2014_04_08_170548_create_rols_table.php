<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rols', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
        
        DB::table('rols')->insert(
        array(
            'name' => 'administrator',
        )
        );
        
        DB::table('rols')->insert(
        array(
            'name' => 'operator',
        )
        );
        
        DB::table('rols')->insert(
        array(
            'name' => 'carrier',
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
        Schema::dropIfExists('rols');
        
    }
}
