<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('messages', function(Blueprint $table)
    {
      $table->bigIncrements('id');
      $table->string('from_name', 255);
      $table->integer('from_id')->unsigned()->nullable();
      $table->foreign('from_id')->references('id')->on('users');
      $table->integer('to_id')->unsigned()->nullable();
      $table->foreign('to_id')->references('id')->on('users');
      $table->tinyInteger('priority')->default(1);
      $table->string('subject', 255);
      $table->text('body');
      $table->boolean('read')->default(false);
      $table->text('settings')->nullable();
      $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
      $table->timestamp('updated_at')->nullable();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::drop('messages');
  }

}
