<?php
namespace App\Models;

use Eloquent, Hash, Crypt;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	protected $softDelete = true;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password','nest_password');

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

	/**
	 * We always want to hash the password
	 *
	 * @param string $value The plain text password
	 */
	public function setPasswordAttribute( $value )
	{
		$this->attributes['password'] = Hash::make( $value );
	}

	/**
	 * We are storing the password encrypted in the database
	 *
	 * @param string $value The unencrypted password
	 */
	public function setNestPasswordAttribute( $value )
	{
		if ( $value !== null ) {
			$value = Crypt::encrypt( $value );
		}
		$this->attributes['nest_password'] = $value;
	}

	/**
	 * We are storing the password encrypted in the database
	 *
	 * @param  string $value The encrypted value
	 * @return mixed
	 */
	public function getNestPasswordAttribute( $value )
	{
		if ( $value !== null ){
			$value = Crypt::decrypt( $value );
		}
		return $value;
	}

	/**
	 * Define the nests table relationship
	 *
	 * @return mixed
	 */
	public function devices()
	{
		return $this->hasMany('App\Models\Device');
	}

}
