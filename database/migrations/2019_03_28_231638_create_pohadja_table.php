<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePohadjaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('pohadja', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->bigInteger('user_id')->unsigned();
          $table->bigInteger('predmet_id')->unsigned();
          $table->timestamps();
      });

      Schema::table('pohadja', function($table) {
        $table->foreign('user_id')
          ->references('id')->on('users')
          ->onDelete('no action')
          ->onUpdate('no action');

        $table->foreign('predmet_id')
          ->references('id')->on('predmet')
          ->onDelete('no action')
          ->onUpdate('no action');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pohadja');
    }
}
