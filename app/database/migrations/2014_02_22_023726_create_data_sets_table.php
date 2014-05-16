<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataSetsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('data_sets', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('device_id')->unsigned()->index();
			$table->integer('first_data_id')->unsigned()->nullable()->index();
			$table->integer('last_data_id')->unsigned()->nullable()->index();

			$table->foreign('device_id')->references('id')->on('devices')
				  ->onDelete('cascade')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('data_sets');
	}

}
