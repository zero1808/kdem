<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDamageSeveritiesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('damage_severities', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('number')->unique();
            $table->string('name');
            $table->string('name_english');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('damage_severities');
    }

}
