<?php

namespace TeenQuotes\Countries\Console;

use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use LaraSetting;
use Log;
use TeenQuotes\Countries\Models\Country;
use TeenQuotes\Countries\Repositories\CountryRepository;
use TeenQuotes\Users\Repositories\UserRepository;

class MostCommonCountryCommand extends ScheduledCommand
{
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
     * @var \TeenQuotes\Countries\Repositories\CountryRepository
     */
    private $countryRepo;

    /**
     * @var \TeenQuotes\Users\Repositories\UserRepository
     */
    private $userRepo;

    /**
     * Create a new command instance.
     */
    public function __construct(CountryRepository $countryRepo, UserRepository $userRepo)
    {
        parent::__construct();

        $this->countryRepo = $countryRepo;
        $this->userRepo = $userRepo;
    }

    /**
     * When a command should run.
     *
     * @param  \Indatus\Dispatcher\Scheduling\Schedulable
     *
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
     * Choose the environment(s) where the command should run.
     *
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
        $mostCommonCountryID = $this->userRepo->mostCommonCountryId();

        // The value was different, update it
        if ($currentMostCommonCountryID != $mostCommonCountryID) {
            $country = $this->countryRepo->findById($mostCommonCountryID);
            Log::info('Most common country updated. This is now '.$country->name.'.');

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
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }
}
