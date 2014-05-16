<?php
namespace App\Models;
use Eloquent;

class DataSet extends Eloquent {

	public $timestamps = false;

	/**
	 * Define the devices table relationship
	 *
	 * @return mixed
	 */
	public function device()
	{
		return $this->belongsTo('App\Models\Device');
	}

	/**
	 * Define the data_points table relationship for the first point
	 *
	 * @return mixed
	 */
	public function firstData()
	{
		return $this->belongsTo('App\Models\DataPoint');
	}

	/**
	 * Define the data_points table relationship for the last point
	 *
	 * @return mixed
	 */
	public function lastData()
	{
		return $this->belongsTo('App\Models\DataPoint');
	}

	/**
	 * Define the data_points table relationship
	 *
	 * @return mixed
	 */
	public function dataPoints()
	{
		return $this->hasMany('App\Models\DataPoint');
	}

	/**
	 * Define the tracking table relationship
	 *
	 * @return mixed
	 */
	public function tracking()
	{
		return $this->hasOne('App\Models\Tracking');
	}

}