<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadsTable extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('leads', function(Blueprint $table)
    {
      $table->bigIncrements('id');
      $table->integer('user_id')->unsigned();
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->bigInteger('site_id')->unsigned()->nullable();
      $table->foreign('site_id')->references('id')->on('sites')->nullable()->onDelete('set null');
      $table->tinyInteger('status')->default(0);
      $table->string('ab_variant', 1)->nullable();
      $table->string('language', 5)->nullable();
      $table->string('email', 64);

      $table->string('ip', 64)->nullable();
      $table->string('os', 64)->nullable();
      $table->string('client', 64)->nullable();
      $table->string('device', 64)->nullable();
      $table->string('brand', 64)->nullable();
      $table->string('model', 64)->nullable();

      $table->mediumText('settings')->nullable();
      $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
      $table->timestamp('updated_at')->nullable();
      $table->softDeletes();
    });

    // Creates the lead_site (Many-to-Many relation) table
    Schema::create('lead_site', function($table)
    {
      $table->bigIncrements('id')->unsigned();
      $table->bigInteger('lead_id')->unsigned();
      $table->bigInteger('site_id')->unsigned();
      $table->foreign('lead_id')->references('id')->on('leads');
      $table->foreign('site_id')->references('id')->on('sites');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('lead_site', function(Blueprint $table) {
      $table->dropForeign('lead_site_site_id_foreign');
      $table->dropForeign('lead_site_lead_id_foreign');
    });
    Schema::drop('lead_site');

    Schema::drop('leads');
  }

}
