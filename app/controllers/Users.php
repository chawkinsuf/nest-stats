<?php
namespace App\Controllers;

use Exception;
use Auth, View, Input, Redirect, Response, Validator;
use App\Models\User, App\Models\Device, App\Models\Tracking;
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

		try {
			$nest = new Nest( Auth::user()->email, Auth::user()->nest_password );
			$locations = $nest->getUserLocations();
		} catch ( Exception $e ){
			return Response::json([ 'error' => true, 'message' => 'Device update failed: '.$e->getMessage() ]);
		}

		$devices = [];
		foreach ( $locations as $location ){
			foreach ( $location->thermostats as $serial ){

				// See if we already have this device in the database
				$device = Device::where( 'serial', '=', $serial )->first();
				if ( $device !== null ){
					$devices[] = $device->toArray();
					continue;
				}

				// Get the device information
				$deviceData = $nest->getDeviceInfo( $serial );

				// Save a new record in the database
				$device = new Device;
				$device->user_id = Auth::user()->id;
				$device->serial = $serial;
				$device->name = $deviceData->name;
				$device->scale = $deviceData->scale;
				$device->postal = $location->postal_code;
				$device->save();

				// Put the record in our list
				$devices[] = $device->toArray();
			}
		}

		return Response::json([ 'message' => 'Devices updated', 'devices' => $devices ]);
	}

	public function getStartTracking(){

		// Get the inputs
		$active = Input::get( 'active' );
		$deviceId = Input::get( 'deviceId' );

		// Check the value of active
		if ( $active != '1' && $active != '0' ){
			return Response::json([ 'error' => true, 'message' => 'Invalid parameters' ]);
		}

		// Get the device model
		$device = Device::find( $deviceId );
		if ( $device === null || $device->user->id != Auth::user()->id ){
			return Response::json([ 'error' => true, 'message' => 'Unauthorized device' ]);
		}

		// Look for a tracking object
		$tracking = $device->tracking()->first();

		// Create one if needed
		if ( $tracking === null ){
			$tracking = new Tracking;
			$tracking->active = $active;
			$tracking->minute = 0;
			$device->tracking()->save( $tracking );
		}

		// Otherwise update it
		else {
			$tracking->active = $active;
			$tracking->save();
		}

		return Response::json([ 'message' => $active ? 'Tracking started' : 'Tracking stopped' ]);
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
