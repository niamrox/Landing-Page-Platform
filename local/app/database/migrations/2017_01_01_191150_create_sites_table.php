<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSitesTable extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    // Site types (Landing Page, Launch Page, Coming Soon)
    Schema::create('site_types', function($table)
    {
      $table->increments('id')->unsigned();
      $table->integer('sort')->unsigned();
      $table->string('name', 64);
      $table->text('icon');
      $table->tinyInteger('icon_width')->unsigned(45);
      $table->boolean('active')->default(true);
    });

    Schema::create('sites', function($table)
    {
      $table->bigIncrements('id');
      $table->tinyInteger('status')->default(1);
      $table->bigInteger('piwik_site_id')->unsigned()->nullable();

      $table->integer('user_id')->unsigned();
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->bigInteger('campaign_id')->unsigned();
      $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('restrict');
      $table->integer('site_type_id')->unsigned();
      $table->foreign('site_type_id')->references('id')->on('site_types');
      $table->string('template', 164)->nullable();
      $table->integer('lead_industry_id')->unsigned()->nullable();
      $table->foreign('lead_industry_id')->references('id')->on('lead_industries');

      $table->string('name', 128);

      $table->string('local_domain', 255)->nullable();
      $table->string('domain', 255)->nullable();
      $table->string('language', 5)->default('en');
      $table->string('timezone', 32)->default('UTC');
      $table->text('robots')->nullable();

      $table->boolean('active')->default(true);
      $table->text('settings')->nullable();
      $table->text('settings_published')->nullable();
      $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
      $table->timestamp('updated_at')->nullable();
      $table->integer('created_by')->nullable();
      $table->integer('updated_by')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {

    Schema::table('sites', function(Blueprint $table) {
      $table->dropForeign('sites_user_id_foreign');
      $table->dropForeign('sites_campaign_id_foreign');
    });
    Schema::drop('sites');

    Schema::drop('site_types');

  }

}
