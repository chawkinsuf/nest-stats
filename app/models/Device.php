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
	 * Define the data_sets table relationship
	 *
	 * @return mixed
	 */
	public function dataSets()
	{
		return $this->hasMany('App\Models\DataSet');
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