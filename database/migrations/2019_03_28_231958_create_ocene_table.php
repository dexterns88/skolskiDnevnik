<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOceneTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ocene', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->bigInteger('pohadja_id')->unsigned();
          $table->Integer('ocena');
          $table->timestamps();
        });

      Schema::table('ocene', function($table) {
        $table->foreign('pohadja_id')
          ->references('id')->on('pohadja')
          ->onDelete('cascade')
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
        Schema::dropIfExists('ocene');
    }
}
