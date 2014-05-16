<?php
namespace App\Models;
use Eloquent;

class Tracking extends Eloquent {

	public $timestamps = false;
	protected $primaryKey = 'device_id';

	/**
	 * Define the device table relationship
	 *
	 * @return mixed
	 */
	public function device()
	{
		return $this->belongsTo('App\Models\Device');
	}

	/**
	 * Define the data_sets table relationship
	 *
	 * @return mixed
	 */
	public function dataSet()
	{
		return $this->belongsTo('App\Models\DataSet');
	}

}