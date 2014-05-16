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
			$table->integer('data_set_id')->unsigned();
			$table->decimal('temperature', 6, 3);
			$table->decimal('outside_temperature', 6, 3)->nullable();
			$table->integer('humidity');
			$table->integer('outside_humidity')->nullable();
			$table->decimal('target_heat', 6, 3)->nullable();
			$table->decimal('target_cool', 6, 3)->nullable();
			$table->boolean('ac');
			$table->boolean('heat');
			$table->boolean('aux_heat');
			$table->boolean('fan');
			$table->timestamp('date');

			$table->index([ 'data_set_id', 'date' ]);
			$table->foreign('data_set_id')->references('id')->on('data_sets')
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
		Schema::drop('data_points');
	}

}
