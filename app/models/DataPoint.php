<?php
namespace App\Models;
use Eloquent;

class DataPoint extends Eloquent {

	public $timestamps = false;

	/**
	 * Define the nests table relationship
	 *
	 * @return mixed
	 */
	public function devices()
	{
		return $this->belongsTo('App\Models\Device');
	}

}