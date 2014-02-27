<?php
namespace App\Models;
use Eloquent;

class Device extends Eloquent {

	/**
	 * Define the users table relationship
	 *
	 * @return mixed
	 */
	public function user()
	{
		return $this->belongsTo('App\Models\User');
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
	 * Make sure this value is actually a boolean value
	 *
	 * @param  integer $value
	 * @return boolean
	 */
	public function getTrackingAttribute( $value )
	{
		return (boolean)$value;
	}

}