<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSitePagesTable extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {

  Schema::create('site_pages', function(Blueprint $table) {
    $table->bigIncrements('id');
    $table->bigInteger('site_id')->unsigned();
    $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');

    $table->bigInteger('parent_id')->nullable()->index();
    $table->bigInteger('lft')->nullable()->index();
    $table->bigInteger('rgt')->nullable()->index();
    $table->integer('depth')->nullable();

    $table->string('name', 255)->nullable();
    $table->text('meta_title')->nullable();
    $table->text('meta_desc')->nullable();
    $table->string('meta_robots', 32)->nullable();

    $table->string('name_published', 255)->nullable();
    $table->text('meta_title_published')->nullable();
    $table->text('meta_desc_published')->nullable();
    $table->string('meta_robots_published', 32)->nullable();

    $table->string('slug', 255)->nullable();
    $table->string('link', 255)->nullable();
    $table->boolean('hidden')->default(false);
    $table->boolean('hidden_parent')->default(false);
    $table->boolean('secured')->default(false);
    $table->boolean('secured_parent')->default(false);

    $table->mediumText('content')->nullable();
    $table->mediumText('content_published')->nullable();
    $table->text('settings')->nullable();
    
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
  public function down() {
    Schema::drop('site_pages');
  }

}
