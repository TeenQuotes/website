<?php

namespace TeenQuotes\Newsletters\Console;

use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Log;
use MandrillClient;
use TeenQuotes\Mail\UserMailer;
use TeenQuotes\Newsletters\NewslettersManager;
use TeenQuotes\Users\Repositories\UserRepository;

class UnsubscribeUsersCommand extends ScheduledCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'newsletter:deleteUsers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unsubscribe users from newsletters.';

    /**
     * @var \TeenQuotes\Users\Repositories\UserRepository
     */
    private $userRepo;

    /**
     * @var \TeenQuotes\Newsletters\NewslettersManager
     */
    private $newslettersManager;

    /**
     * @var \TeenQuotes\Mail\UserMailer
     */
    private $userMailer;

    /**
     * Create a new command instance.
     */
    public function __construct(UserRepository $userRepo, NewslettersManager $newslettersManager, UserMailer $userMailer)
    {
        parent::__construct();

        $this->newslettersManager = $newslettersManager;
        $this->userMailer = $userMailer;
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
            ->hours(17)
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
     */
    public function fire()
    {
        // Retrieve inactive users
        $nonActiveUsers = $this->userRepo->getNonActiveHavingNewsletter();

        $hardBouncedUsers = $this->getHardBouncedUsers();

        // Merge all users that need to be unsubscribed from newsletters
        $allUsers = $nonActiveUsers->merge($hardBouncedUsers);

        // Unsubscribe these users from newsletters
        $this->newslettersManager->deleteForUsers($allUsers);

        // Send an email to each user to notice them
        $nonActiveUsers->each(function ($user) {
            // Log this info
            $this->writeToLog('Unsubscribing user from newsletters: '.$user->login.' - '.$user->email);

            // Send the actual email
            $this->userMailer->unsubscribeUserFromNewsletter($user);
        });
    }

    /**
     * Get users that have already been affected by a hard bounce.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getHardBouncedUsers()
    {
        $users = MandrillClient::getHardBouncedUsers();

        // Delete each user from the existing rejection list
        $instance = $this;
        $users->each(function ($user) use ($instance) {
            MandrillClient::deleteEmailFromRejection($user->email);

            // Log this info
            $instance->writeToLog('Removing user from the rejection list: '.$user->login.' - '.$user->email);
        });

        return $users;
    }

    private function writeToLog($line)
    {
        $this->info($line);
        Log::info($line);
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
