<?php
namespace App\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Models\User, App\Models\Device, App\Models\DataPoint;
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
	protected $description = 'Command description.';

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
		$user = User::where( 'email', '=', 'chawkinsuf@gmail.com' )->first();
		$nest = new Nest( $user->email, $user->nest_password );
		$device = $user->devices()->first();

		$lastDate = -1;
		while ( true ){

			$date = date('i');
			if ( $lastDate == $date || $date % 10 != 0 ){
				sleep( 5 );
				continue;
			}

			$lastDate = $date;

			// Get the device information and store it
			$ts = date('Y-m-d H:i:s');
			$deviceData = $nest->getDeviceInfo( $device->serial );
			$weatherData = $nest->getWeather( $device->postal );

			$dataPoint = new DataPoint;
			$dataPoint->device_id = $device->id;
			$dataPoint->temperature = $deviceData->current_state->temperature;
			$dataPoint->outside_temperature = $weatherData->outside_temperature;
			$dataPoint->humidity = $deviceData->current_state->humidity;
			$dataPoint->outside_humidity = $weatherData->outside_humidity;
			$dataPoint->target_temperature = is_array( $deviceData->target->temperature ) ? $deviceData->target->temperature[1] : $deviceData->target->temperature;
			$dataPoint->ac = $deviceData->current_state->ac;
			$dataPoint->heat = $deviceData->current_state->heat;
			$dataPoint->alt_heat = $deviceData->current_state->alt_heat;
			$dataPoint->fan = $deviceData->current_state->fan;
			$dataPoint->date = $ts;
			$dataPoint->save();

			var_dump( $dataPoint->toArray() );
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
