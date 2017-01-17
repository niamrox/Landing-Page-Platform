<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('order_coupons', function($table)
    {
      $table->bigIncrements('id');
      $table->integer('reseller_id')->unsigned()->nullable();
      $table->foreign('reseller_id')->references('id')->on('resellers')->onDelete('cascade');
      $table->integer('user_id')->unsigned()->nullable();
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->string('name', 64);
      $table->string('code', 32);
      $table->integer('redeemed')->unsigned()->default(0);
      $table->integer('max_redeemed')->unsigned()->default(0);
      $table->integer('discount')->unsigned();
      $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
      $table->timestamp('updated_at')->nullable();
      $table->text('meta')->nullable();
    });

    Schema::create('orders', function($table)
    {
      $table->bigIncrements('id');
      $table->integer('reseller_id')->unsigned();
      $table->foreign('reseller_id')->references('id')->on('resellers')->onDelete('cascade');
      $table->integer('user_id')->unsigned();
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->string('user_name', 32)->nullable();
      $table->string('user_mail', 32)->nullable();
      $table->bigInteger('invoice')->unsigned();
      $table->date('invoice_date')->nullable();
      $table->timestamp('invoice_datetime')->nullable();
      $table->bigInteger('plan_id')->unsigned();
      $table->integer('plan_monthly')->unsigned();
      $table->integer('plan_annual')->unsigned();
      $table->string('plan_name', 32);
      $table->date('expires')->nullable();
      $table->string('period', 32)->nullable();
      $table->string('payment_method', 32);
      $table->integer('cost')->unsigned();
      $table->string('cost_str', 32);
      $table->integer('discount')->unsigned()->default(0);
      $table->string('coupon', 32)->nullable();
      $table->string('status', 32);
      $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
      $table->timestamp('updated_at')->nullable();
      $table->text('meta')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::drop('orders');
    Schema::drop('order_coupons');
  }

}