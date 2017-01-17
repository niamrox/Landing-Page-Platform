<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOauthTable extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('oauth', function($table)
    {
      $table->bigIncrements('id');
      $table->integer('user_id')->unsigned();
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->string('uuid', 64)->nullable();
      $table->string('provider', 64)->nullable();
      $table->string('access_token_secret', 250)->nullable();
      $table->string('oauth1_token_identifier', 250)->nullable();
      $table->string('oauth1_token_secret', 250)->nullable();
      $table->string('oauth2_access_token', 250)->nullable();
      $table->string('oauth2_refresh_token', 250)->nullable();
      $table->timestamp('oauth2_expires')->nullable();
      $table->text('settings')->nullable();
      $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
      $table->timestamp('updated_at')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('oauth', function(Blueprint $table) {
      $table->dropForeign('oauth_user_id_foreign');
    });
  }

}
