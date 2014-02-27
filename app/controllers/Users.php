<?php
namespace App\Controllers;

use Auth, View, Input, Redirect, Response, Validator;
use App\Models\User, App\Models\Device;
use NestApi\Nest;

class Users extends Base {
	protected $layout = "layouts.main";

	public function __construct(){
		$this->beforeFilter( 'csrf', [ 'on' => 'post' ] );
		$this->beforeFilter( 'auth', [ 'except' => [ 'getLogin', 'postLogin' ] ] );
	}

	public function getLogin(){

		// No need to be here if we are logged in
		if ( Auth::check() ){
			return Redirect::to( 'users/profile' );
		}

		// If the login fails, the previous email value will be sent back
		$data = [
			'email' => Input::old( 'email' )
		];

		// Build the login form
		$this->layout->content = View::make( 'users.login', $data );
	}

	public function postLogin(){

		// Try to login and redirect if successful
		if ( Auth::attempt([ 'email' => Input::get( 'email' ), 'password' => Input::get( 'password' ) ]) ){
			return Redirect::intended( 'users/profile' )->with( 'message', 'Let\'s save some energy!' );
		}

		// Otherwise go back to the login page
		return Redirect::to( 'users/login' )
			->with( 'message', 'Your username/password combination was incorrect' )
			->withInput();
	}

	public function getLogout(){

		// Process the logout
		Auth::logout();

		// Redirect to the login page
		return Redirect::to( 'users/login' )
			->with( 'message', 'You have been logged out of the system' );
	}

	public function getDevices(){
		$nest = new Nest( Auth::user()->email, Auth::user()->nest_password );
		$locations = $nest->getUserLocations();
		$devices = [];
		foreach ( $locations as $location ){
			foreach ( $location->thermostats as $serial ){

				// See if we already have this device in the database
				$device = Device::where( 'serial', '=', $serial )->first();
				if ( $device !== null ){
					$devices[] = $device->toArray();
					continue;
				}

				// Get the device information and store it
				$deviceData = $nest->getDeviceInfo( $serial );
				$device = new Device;
				$device->user_id = Auth::user()->id;
				$device->serial = $serial;
				$device->name = $deviceData->name;
				$device->postal = $location->postal_code;
				$device->tracking = false;
				$device->save();
				$devices[] = $device->toArray();
			}
		}

		return Response::json([ 'success' => true, 'devices' => $devices ]);
	}

	public function getProfile(){
		$devices = Auth::user()->devices()->get();
		$data = [
			'devices' => $devices,
			'devicesData' => $devices->toJson()
		];
		$this->layout->content = View::make( 'users.profile', $data );
	}

	public function postProfile(){

		// Setup our validation rules
		$rules = [
			'email' => 'required|email|unique:users,email,'.Auth::user()->id,
			'password' => 'min:6|confirmed',
			'password_confirmation' => 'min:6',
			'nest_password' => ''
		];

		// Run the validation
		$validator = Validator::make( Input::all(), $rules );
		if ( $validator->fails() ){
			return Redirect::to( 'users/profile' )
				->withInput()
				->withErrors( $validator );
		}

		// Update the record
		$user = Auth::user();
		$user->email = Input::get( 'email' );
		if ( Input::get( 'nest_password' ) != '' ){
			$user->nest_password = Input::get( 'nest_password' );
		}
		if ( Input::get( 'password' ) != '' ){
			$user->password = Input::get( 'password' );
		}
		$user->save();

		return Redirect::to( 'users/profile' )->with( 'message', 'Your profile has been updated' );
	}
}
