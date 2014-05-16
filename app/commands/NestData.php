<?php
namespace App\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Carbon\Carbon;

use App\Models\User, App\Models\Device, App\Models\Tracking, App\Models\DataSet, App\Models\DataPoint;
use NestApi\Nest;

class NestData extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nest:data';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Start record data from Nest devices.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$dataInterval = 2;
		$trackingDevices = Tracking::where( 'active', '=', '1' )->get();

		$lastDate = -1;
		while ( true ){

			$date = date('i');
			if ( $lastDate == $date || $date % $dataInterval != 0 ){

				// Take a break
				sleep( 10 );

				// Update the list of devices we are tracking
				$trackingDevices = Tracking::where( 'active', '=', '1' )->get();

				// Move on
				continue;
			}
			$lastDate = $date;

			foreach ( $trackingDevices as $trackingDevice ){

				// Setup some variables
				$dataSet = $trackingDevice->data_set;
				$device = $trackingDevice->device;
				$user = $device->user;

				// Open the nest connection
				$nest = new Nest( $user->email, $user->nest_password );

				// If we don't have a data set or our data interval is out of whack, we need a new data set
				if ( $dataSet === null || ( $dataSet->last_data !== null && Carbon::now()->diffInMinutes( $dataSet->last_data->date ) > $dataInterval ) ){
					$dataSet = new DataSet;
					$trackingDevice->device->dataSets()->save( $dataSet );
					$trackingDevice->dataSet()->associate( $dataSet );
					$trackingDevice->save();
				}

				print "\nNew Data Point\n";
				$ts = date('Y-m-d H:i:s');

				// Get the device and weather information
				$deviceData = $nest->getDeviceInfo( $device->serial );
				$weatherData = $nest->getWeather( $device->postal );
				//var_dump( $deviceData );

				// Handle different variations for the target temperature
				switch ( $deviceData->target->mode ){
					case 'range':
						$target_heat = $deviceData->target->temperature[0];
						$target_cool = $deviceData->target->temperature[1];
						break;
					case 'heat':
						$target_heat = $deviceData->target->temperature;
						$target_cool = null;
						break;
					case 'cool':
						$target_heat = null;
						$target_cool = $deviceData->target->temperature;
						break;
					default:
						$target_heat = null;
						$target_cool = null;
				}

				// Make the data record
				$dataPoint = new DataPoint;
				$dataPoint->data_set_id = $dataSet->id;
				$dataPoint->temperature = $deviceData->current_state->temperature;
				$dataPoint->outside_temperature = $weatherData->outside_temperature;
				$dataPoint->humidity = $deviceData->current_state->humidity;
				$dataPoint->outside_humidity = $weatherData->outside_humidity;
				$dataPoint->target_heat = $target_heat;
				$dataPoint->target_cool = $target_cool;
				$dataPoint->ac = $deviceData->current_state->ac;
				$dataPoint->heat = $deviceData->current_state->heat;
				$dataPoint->aux_heat = $deviceData->current_state->alt_heat;
				$dataPoint->fan = $deviceData->current_state->fan;
				$dataPoint->date = $ts;
				$dataPoint->save();

				// Update the data set
				$dataSet->lastData()->associate( $dataPoint );
				if ( $dataSet->first_data === null ){
					$dataSet->firstData()->associate( $dataPoint );
				}
				$dataSet->save();

				var_dump( $dataPoint->toArray() );
			}
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [];
		/*
		return array(
			array('example', InputArgument::REQUIRED, 'An example argument.'),
		);
		*/
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [];
		/*
		return array(
			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
		*/
	}

}
