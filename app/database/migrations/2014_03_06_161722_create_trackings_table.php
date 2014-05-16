<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrackingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('trackings', function(Blueprint $table)
		{
			$table->integer('device_id')->unsigned()->primary();
			$table->integer('data_set_id')->unsigned()->nullable()->index();
			$table->boolean('active');
			$table->integer('minute');

			$table->foreign('device_id')->references('id')->on('devices')
				  ->onDelete('cascade')->onUpdate('cascade');
			$table->foreign('data_set_id')->references('id')->on('data_sets')
				  ->onDelete('set null')->onUpdate('set null');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('trackings');
	}

}
