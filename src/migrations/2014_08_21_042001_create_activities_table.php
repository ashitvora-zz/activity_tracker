<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivitiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activities', function($table)
    {
      $table->increments('id');
      $table->integer('actor_id')->index();
      $table->string('trackable_type', 64)->index();
      $table->integer('trackable_id');
      $table->string('action', 32);
      $table->text('details');
      $table->timestamps();
    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('activities');
	}

}
