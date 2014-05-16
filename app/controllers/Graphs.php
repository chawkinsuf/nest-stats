<?php
namespace App\Controllers;
use Exception;
use Auth, View, Response, DB;
use App\Models\Device, App\Models\DataSet, App\Models\DataPoint;

class Graphs extends Base {
	protected $layout = "layouts.main";

	public function __construct(){
		$this->beforeFilter( 'csrf', [ 'on' => 'post' ] );
		$this->beforeFilter( 'auth' );
	}

	public function getTemperature(){

		$tree = $this->generateDataTree();
		$data = [
			'dataSets' => json_encode( $tree )
		];
		$this->layout->content = View::make( 'graphs.temperature', $data );
	}

	public function getHumidity(){
		$tree = $this->generateDataTree();
		$data = [
			'dataSets' => json_encode( $tree )
		];
		$this->layout->content = View::make( 'graphs.humidity', $data );
	}

	protected function generateDataTree(){

		// Get the list of devices and include the data set
		$devices = Device::with('dataSets', 'dataSets.firstData', 'dataSets.lastData')->where( 'user_id', Auth::user()->id )->get();

		// Initialize the tree
		$tree = [
			'text' => 'Select Data Set',
			'icon' => 'none',
			'nodes' => []
		];

		// Process each device
		foreach ( $devices as $device ){
			$deviceNode = [
				'text' => "$device->name &ndash; $device->postal",
				'icon' => 'none',
				'nodes' => []
			];
			foreach ( $device->data_sets as $data_set ){
				$first = substr( $data_set['first_data']['date'], 0, -3 );
				$last  = substr( $data_set['last_data' ]['date'], 0, -3 );
				$dataNode = [
					'text' => "$first &ndash; $last",
					'dataSetId' => $data_set->id
				];
				$deviceNode['nodes'][] = $dataNode;
			}
			$tree['nodes'][] = $deviceNode;
		}
		return [ $tree ];
	}

	public function getData( $dataSetId = null ){

		// Get the most recent data set if we don't have one selected
		if ( $dataSetId === null ){
			$dataSetId = Auth::user()->devices->first()->data_sets->last()->id;
		}

		// Get our raw data set
		$dataPointsRaw = DB::table('data_points')
			->select('data_points.*')
			->join('data_sets', 'data_sets.id', '=', 'data_points.data_set_id')
			->join('devices', 'devices.id', '=', 'data_sets.device_id')
			->where( 'devices.user_id', '=', Auth::user()->id )
			->where( 'data_points.data_set_id', '=', $dataSetId )
			->get();

		// Convert our raw data into eloquent models
		$dataPoints = new \Illuminate\Database\Eloquent\Collection;
		foreach ( $dataPointsRaw as $dataPointRaw ){
			$dataPoints->add( ( new DataPoint )->newFromBuilder( $dataPointRaw ) );
		}

		$data = [
			'temperature' 			=> [],
			'outside_temperature' 	=> [],
			'humidity' 				=> [],
			'fan' 					=> [],
			'ac' 					=> [],
			'heat' 					=> [],
			'aux_heat' 				=> [],
		];

		foreach ( $dataPoints as $dataPoint ){
			$datems = 1000 * $dataPoint->date->timestamp;

			$data['temperature'][] 			= [ $datems, $this->parseData( $dataPoint->temperature,			'float' ) ];
			$data['outside_temperature'][] 	= [ $datems, $this->parseData( $dataPoint->outside_temperature, 'float' ) ];
			$data['humidity'][] 			= [ $datems, $this->parseData( $dataPoint->humidity, 			'int' ) ];
			$data['outside_humidity'][] 	= [ $datems, $this->parseData( $dataPoint->outside_humidity, 	'int' ) ];

			$data['fan'][] 					= [ $datems, $this->parseData( $dataPoint->fan, 				'int' ) ];
			$data['ac'][] 					= [ $datems, $this->parseData( $dataPoint->ac, 					'int' ) ];
			$data['heat'][] 				= [ $datems, $this->parseData( $dataPoint->heat, 				'int' ) ];
			$data['aux_heat'][] 			= [ $datems, $this->parseData( $dataPoint->aux_heat, 			'int' ) ];
		}

		return Response::json( $data );
	}

	protected function parseData( $data, $type ){

		if ( $data === null ){
			return null;
		}

		switch ( $type ){
			case 'int':
				return (int) $data;
				break;

			case 'float':
				return (float) $data;
				break;

			default:
				throw new Exception("Invalid data type $type");
		}
	}
}