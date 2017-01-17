<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKeysTable extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('keys', function($table)
    {
      $table->increments('id')->unsigned();
      $table->text('key');
      $table->text('match');
      $table->integer('expire');
      $table->boolean('active')->defeult(true);
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::drop('keys');
  }

}