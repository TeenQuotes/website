<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Users\Console;

use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Log;
use TeenQuotes\Mail\UserMailer;
use TeenQuotes\Users\Repositories\UserRepository;

class SendBirthdayCommand extends ScheduledCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'birthday:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Whish happy birthday to the concerned users.';

    /**
     * @var \TeenQuotes\Users\Repositories\UserRepository
     */
    private $userRepo;

    /**
     * @var \TeenQuotes\Mail\UserMailer
     */
    private $userMailer;

    /**
     * Create a new command instance.
     */
    public function __construct(UserRepository $userRepo, UserMailer $userMailer)
    {
        parent::__construct();

        $this->userRepo   = $userRepo;
        $this->userMailer = $userMailer;
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
            ->hours(12)
            ->minutes(30);
    }

    /**
     * Choose the environment(s) where the command should run.
     *
     * @return array Array of environments' name
     */
    public function environment()
    {
        return ['production'];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $users = $this->userRepo->birthdayToday();

        $users->each(function ($user) {
            $this->log('Wishing happy birthday to '.$user->login.' - '.$user->email);

            $this->userMailer->wishHappyBirthday($user);
        });
    }

    private function log($string)
    {
        $this->info($string);
        Log::info($string);
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
