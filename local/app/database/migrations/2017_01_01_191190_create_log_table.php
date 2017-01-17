<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogTable extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('logs', function(Blueprint $table)
    {
      $table->bigIncrements('id');
      $table->integer('user_id')->unsigned();
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->integer('parent_id')->unsigned()->nullable();
      $table->foreign('parent_id')->references('id')->on('users')->onDelete('cascade');
      $table->string('type', 64);
      $table->string('subject', 64);
      $table->string('desc', 255);
      $table->string('ip', 64);
      $table->string('os', 64)->nullable();
      $table->string('client', 64)->nullable();
      $table->string('device', 64)->nullable();
      $table->string('brand', 64)->nullable();
      $table->string('model', 64)->nullable();

      $table->timestamp('created_at')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::drop('logs');
  }

}
