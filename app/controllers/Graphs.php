<?php
namespace App\Controllers;
use Auth, View, Response;

class Graphs extends Base {
	protected $layout = "layouts.main";

	public function __construct(){
		$this->beforeFilter( 'csrf', [ 'on' => 'post' ] );
		$this->beforeFilter( 'auth' );
	}

	public function getTemperature(){
		$this->layout->content = View::make( 'graphs.temperature' );
	}

	public function getHumidity(){
		$this->layout->content = View::make( 'graphs.humidity' );
	}

	public function getData(){

		$dataPoints = Auth::user()->devices()->first()->dataPoints()->get();

		$data = [
			'temperature' 			=> [],
			'outside_temperature' 	=> [],
			'humidity' 				=> [],
			'fan' 					=> [],
			'ac' 					=> [],
			'heat' 					=> [],
			'alt_heat' 				=> [],
		];

		foreach ( $dataPoints as $dataPoint ){
			$datems = 1000 * strtotime( $dataPoint->date );

			$data['temperature'][] 			= [ $datems, (float) $dataPoint->temperature ];
			$data['outside_temperature'][] 	= [ $datems, (float) $dataPoint->outside_temperature ];
			$data['humidity'][] 			= [ $datems, (float) $dataPoint->humidity ];
			$data['outside_humidity'][] 	= [ $datems, (float) $dataPoint->outside_humidity ];

			$data['fan'][] 					= [ $datems, (float) $dataPoint->fan ];
			$data['ac'][] 					= [ $datems, (float) $dataPoint->ac ];
			$data['heat'][] 				= [ $datems, (float) $dataPoint->heat ];
			$data['alt_heat'][] 			= [ $datems, (float) $dataPoint->alt_heat ];
		}

		return Response::json( $data );
	}
}