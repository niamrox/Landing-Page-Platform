<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConfideSetupUsersTable extends Migration {

  /**
   * Run the migrations.
   *
   * @return  void
   */
  public function up()
  {
    // Resellers
    Schema::create('resellers', function(Blueprint $table)
    {
      $table->increments('id');
      $table->string('domain', 250)->nullable();
      $table->string('mail_from_address', 64)->nullable();
      $table->string('mail_from_name', 64)->nullable();
      $table->string('contact_business', 64)->nullable();
      $table->string('contact_name', 64)->nullable();
      $table->string('contact_mail', 64)->nullable();
      $table->string('contact_phone', 64)->nullable();
      $table->string('contact_address1', 64)->nullable();
      $table->string('contact_address2', 64)->nullable();
      $table->string('contact_zip', 64)->nullable();
      $table->string('contact_city', 64)->nullable();
      $table->string('contact_region', 64)->nullable();
      $table->string('contact_country', 64)->nullable();
      $table->dateTime('expires')->nullable();
      $table->boolean('active')->default(true);
      $table->text('settings')->nullable();
      $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
      $table->timestamp('updated_at')->nullable();
    });

    // Plans
    Schema::create('plans', function(Blueprint $table)
    {
      $table->increments('id');
      $table->integer('reseller_id')->unsigned()->nullable();
      $table->foreign('reseller_id')->references('id')->on('resellers');
      $table->integer('sort')->default(0);
      $table->string('name', 100);
      $table->boolean('hidden')->default(false);
      $table->boolean('undeletable')->default(false);
      $table->mediumText('settings')->nullable();
    });

    // Creates the users table
    Schema::create('users', function(Blueprint $table)
    {
      $table->increments('id');
      $table->integer('reseller_id')->unsigned()->nullable();
      $table->foreign('reseller_id')->references('id')->on('resellers')->onDelete('cascade');
      $table->boolean('reseller')->default(false);
      $table->integer('parent_id')->unsigned()->nullable();
      $table->foreign('parent_id')->references('id')->on('users')->onDelete('cascade');
      $table->bigInteger('remote_id')->nullable();
      $table->integer('plan_id')->unsigned()->nullable();
      $table->foreign('plan_id')->references('id')->on('plans');
      $table->string('username', 32);
      $table->string('email', 64);
      $table->string('password', 64);
      $table->string('confirmation_code', 128)->nullable();
      $table->string('remember_token', 128)->nullable();
      $table->boolean('confirmed')->default(false);

      $table->string('first_name', 32)->nullable();
      $table->string('last_name', 32)->nullable();
      $table->string('website')->nullable();
      $table->string('twitter', 128)->nullable();
      $table->string('facebook', 128)->nullable();

      $table->string('provider', 128)->nullable();
      $table->string('theme', 16)->nullable();
      $table->string('language', 5)->default('en');
      $table->string('timezone', 32)->default('UTC');
      $table->integer('logins')->default(0)->unsigned();
      $table->dateTime('last_login')->nullable();
      $table->text('settings')->nullable();
      $table->dateTime('expires')->nullable();
      $table->smallInteger('expire_notifications')->default(0)->unsigned();

      $table->string('avatar_file_name')->nullable();
      $table->integer('avatar_file_size')->nullable();
      $table->string('avatar_content_type')->nullable();
      $table->timestamp('avatar_updated_at')->nullable();

      $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
      $table->timestamp('updated_at')->nullable();
    });

    // Creates password reminders table
    Schema::create('password_reminders', function(Blueprint  $table)
    {
      $table->string('email');
      $table->string('token');
      $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return  void
   */
  public function down()
  {
    Schema::drop('password_reminders');
    Schema::drop('users');
    Schema::drop('plans');
  }

}
