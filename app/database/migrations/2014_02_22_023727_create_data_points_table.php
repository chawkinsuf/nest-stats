<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataPointsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('data_points', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('device_id')->unsigned();
			$table->decimal('temperature', 6, 3);
			$table->decimal('outside_temperature', 6, 3);
			$table->integer('humidity');
			$table->integer('outside_humidity');
			$table->decimal('target_temperature', 6, 3);
			$table->boolean('ac');
			$table->boolean('heat');
			$table->boolean('alt_heat');
			$table->boolean('fan');
			$table->timestamp('date');

			$table->index([ 'device_id', 'date' ]);
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
		Schema::drop('data_point');
	}

}
