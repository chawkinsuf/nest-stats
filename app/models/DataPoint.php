<?php
namespace App\Models;
use Eloquent;

class DataPoint extends Eloquent {

	public $timestamps = false;

	/**
	 * Define the data_sets table relationship
	 *
	 * @return mixed
	 */
	public function dataSet()
	{
		return $this->belongsTo('App\Models\DataSet');
	}

	/**
	 * Use Carbon instances for the date field
	 *
	 * @return array
	 */
	public function getDates()
	{
		return array('date');
	}
}