<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DataSetsForeignKeys extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('data_sets', function(Blueprint $table)
		{
			$table->foreign('first_data_id')->references('id')->on('data_points')
				  ->onDelete('cascade')->onUpdate('cascade');
			$table->foreign('last_data_id')->references('id')->on('data_points')
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
		Schema::table('data_sets', function(Blueprint $table)
		{
			$table->dropForeign('data_sets_first_data_id_foreign');
			$table->dropForeign('data_sets_last_data_id_foreign');
		});
	}

}