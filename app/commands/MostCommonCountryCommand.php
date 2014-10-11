<?php

use Illuminate\Console\Command;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use TeenQuotes\Countries\Models\Country;

class MostCommonCountryCommand extends ScheduledCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'countries:mostCommon';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Compute the most common country ID and update it in settings.json.';

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
	 * When a command should run
	 *
	 * @param Scheduler $scheduler
	 * @return \Indatus\Dispatcher\Scheduling\Schedulable
	 */
	public function schedule(Schedulable $scheduler)
	{
		return $scheduler
			->daily()
			->hours(18)
			->minutes(00);
	}

	/**
	 * Choose the environment(s) where the command should run
	 * @return array Array of environments' name
	 */
	public function environment()
	{
		return ['production', 'staging'];
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$currentMostCommonCountryID = Country::getDefaultCountry();
		
		// Get the most common country in the users' table
		$dummyUser = User::select('country', DB::raw('count(*) as total'))
			->groupBy('country')
			->orderBy('total', 'DESC')
			->first();
		$mostCommonCountryID = (int) $dummyUser->country;
		
		// The value was different, update it
		if ($currentMostCommonCountryID != $mostCommonCountryID) {
			
			$country = Country::find($mostCommonCountryID);
			Log::info("Most common country updated. This is now ".$country->name.".");
			
			LaraSetting::set('countries.defaultCountry', $mostCommonCountryID);
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}
}
